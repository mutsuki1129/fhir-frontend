import json
import os
from pathlib import Path

from playwright.sync_api import TimeoutError as PlaywrightTimeoutError
from playwright.sync_api import sync_playwright


BASE_URL = os.environ.get("FHIR_DEMO_BASE_URL", "http://localhost:8080").rstrip("/")
DEMO_EMAIL = os.environ.get("FHIR_DEMO_EMAIL")
DEMO_PASSWORD = os.environ.get("FHIR_DEMO_PASSWORD")
PLAYWRIGHT_CHANNEL = os.environ.get("FHIR_PLAYWRIGHT_CHANNEL")
PLAYWRIGHT_EXECUTABLE = os.environ.get("FHIR_PLAYWRIGHT_EXECUTABLE")
PACKAGE_DIR = Path(__file__).resolve().parent
SCREENSHOT_DIR = PACKAGE_DIR / "screenshots"

PUBLIC_PAGES = [
    ("home", "/"),
    ("login", "/login"),
]

AUTH_PAGES = [
    ("lesions-index", "/lesions"),
    ("lesion-detail", "/lesions/lesion-001"),
    ("fhir-metadata", "/fhir"),
]

VIEWPORTS = {
    "desktop": {"width": 1440, "height": 900, "is_mobile": False},
    "mobile": {"width": 390, "height": 844, "is_mobile": True},
}


def page_summary(page, status, screenshot_name, console_errors, page_errors):
    anchors_and_buttons = page.locator("a, button").all_text_contents()
    write_action_labels = [
        text.strip()
        for text in anchors_and_buttons
        if text.strip().lower() in {
            "create",
            "edit",
            "delete",
            "upload",
            "save",
            "approve",
            "sign off",
            "新增",
            "編輯",
            "刪除",
            "上傳",
            "儲存",
            "核准",
            "簽核",
        }
    ]

    content = page.content()

    return {
        "url": page.url,
        "http_status": status,
        "title": page.title(),
        "screenshot": screenshot_name,
        "console_error_count": len(console_errors),
        "page_error_count": len(page_errors),
        "translation_key_leak": "fhir.home_" in content,
        "marketplace_wording": "marketplace" in content.lower(),
        "developer_heavy_first_screen_wording": any(
            marker in content for marker in ["Phase 2 docs", "testing command index", "CDS Runtime Action Allowlist"]
        ),
        "write_action_labels": write_action_labels,
    }


def capture_page(page, viewport_name, page_key, path, results):
    console_errors = []
    page_errors = []
    page.on("console", lambda message: console_errors.append(message.text) if message.type == "error" else None)
    page.on("pageerror", lambda error: page_errors.append(str(error)))

    response = page.goto(f"{BASE_URL}{path}", wait_until="networkidle", timeout=30000)
    status = response.status if response else None
    screenshot_name = f"{viewport_name}-{page_key}.png"
    page.screenshot(path=SCREENSHOT_DIR / screenshot_name, full_page=True)

    results.append(
        {
            "viewport": viewport_name,
            "page": page_key,
            **page_summary(page, status, screenshot_name, console_errors, page_errors),
        }
    )


def login(page):
    if not DEMO_EMAIL or not DEMO_PASSWORD:
        raise RuntimeError("FHIR_DEMO_EMAIL and FHIR_DEMO_PASSWORD are required for authenticated captures.")

    page.goto(f"{BASE_URL}/login", wait_until="networkidle", timeout=30000)
    page.locator('input[name="email"]').fill(DEMO_EMAIL)
    page.locator('input[name="password"]').fill(DEMO_PASSWORD)
    page.locator('button[type="submit"]').click()

    try:
        page.wait_for_url("**/dashboard", timeout=10000)
    except PlaywrightTimeoutError:
        page.wait_for_load_state("networkidle", timeout=10000)

    if "/login" in page.url:
        raise RuntimeError("Login did not leave /login; authenticated screenshots were not captured.")


def main():
    SCREENSHOT_DIR.mkdir(parents=True, exist_ok=True)
    results = {
        "base_url": BASE_URL,
        "credentials": "existing documented demo account; password not recorded",
        "browser": {
            "channel": PLAYWRIGHT_CHANNEL or "bundled",
            "executable": PLAYWRIGHT_EXECUTABLE or None,
        },
        "captures": [],
    }

    with sync_playwright() as playwright:
        launch_options = {"headless": True}
        if PLAYWRIGHT_CHANNEL:
            launch_options["channel"] = PLAYWRIGHT_CHANNEL
        if PLAYWRIGHT_EXECUTABLE:
            launch_options["executable_path"] = PLAYWRIGHT_EXECUTABLE

        browser = playwright.chromium.launch(**launch_options)

        for viewport_name, viewport in VIEWPORTS.items():
            context = browser.new_context(
                viewport={"width": viewport["width"], "height": viewport["height"]},
                is_mobile=viewport["is_mobile"],
                device_scale_factor=2 if viewport["is_mobile"] else 1,
                locale="en-US",
            )
            page = context.new_page()

            for page_key, path in PUBLIC_PAGES:
                capture_page(page, viewport_name, page_key, path, results["captures"])

            login(page)

            for page_key, path in AUTH_PAGES:
                capture_page(page, viewport_name, page_key, path, results["captures"])

            context.close()

        browser.close()

    (PACKAGE_DIR / "visual-qa-results.json").write_text(
        json.dumps(results, indent=2, ensure_ascii=False) + "\n",
        encoding="utf-8",
    )


if __name__ == "__main__":
    main()
