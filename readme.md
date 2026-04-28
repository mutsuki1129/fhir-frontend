# FHIR Frontend（Phase 2）

本專案是 Laravel + Blade 前端，採「在既有介面上逐步 FHIR 化」策略。  
目前以不破壞 Phase 1 已上線行為為前提，推進 Phase 2 第一優先：`Condition`。

## 專案定位

- 應用型態：Laravel Blade Web（前端頁面 + Laravel controller/facade 串接）
- 現況重點：
  - Phase 1：`Patient` + 體溫 `Observation` 已可用
  - Phase 2（進行中）：將 `kondisi` 從 legacy note 過渡到 `Condition`
- 原則：
  - 不做大改版 UI
  - 不中斷既有 Observation 流程
  - 新功能優先採增量式與可回退設計

## Phase 2 範圍與優先順序

1. Priority 1：`Condition`（取代 legacy `kondisi` 為主要臨床欄位）
2. Priority 2：`Media` / `DocumentReference`（接手 legacy `picture`）
3. Priority 3：`Practitioner`（醫師模組 FHIR 對齊）

## Phase 2 目前完成（Frontend）

- 已新增 Phase 2 規劃文件：
  - `PHASE2_TASKS.md`
  - `PHASE2_CONDITION_PLAN.md`
- 已加入 Condition adapter：
  - `app/ViewModels/ConditionVM.php`
  - `app/Support/Fhir/ConditionMapper.php`
- 病例頁最小可用 Condition 流程已上線（保留 Phase 1 行為）：
  - `/rekam/create`：可送 `condition_code`、`condition_text`
  - `/rekam/{id}/edit`：可載入/更新 Condition 欄位
  - `/rekam`：可顯示 Condition 摘要（text/code）
- fallback 策略：
  - Condition API 不可用時，Observation 仍可存取
  - 畫面顯示警示訊息
  - legacy note 仍保留可見

## 邊界與非目標（目前）

- 不移除 Phase 1 既有 Observation（體溫）主流程
- 不做 destructive migration 或 destructive git 操作
- 不在此批次完成 `Media` / `DocumentReference` 與 `Practitioner` 全量改造
- 不做大規模 UI 重設計

## 技術棧

- PHP 8 + Laravel
- Blade Components
- Tailwind CSS
- Vite
- Alpine.js
- MySQL
- Docker Compose（本機開發可選）

## 啟動方式

### 本機模式

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm run dev
```

- App：`http://localhost:8000`
- Vite：`http://localhost:5173`

### Docker 模式

```bash
cp .env.docker .env
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d
docker compose exec app php artisan migrate --seed
```

- App：`http://localhost:8080`
- Vite：`http://localhost:5173`
- phpMyAdmin：`http://localhost:8081`

## 主要環境變數

- `APP_URL`
- `FHIR_BASE_URL`
- `FHIR_TIMEOUT_SECONDS`
- `FHIR_PATIENT_IDENTIFIER_SYSTEM`
- `FHIR_PHASE1_ENABLED`（保留作為相容性旗標）

## 文件索引（建議先看）

- `PHASE2_TASKS.md`
- `PHASE2_CONDITION_PLAN.md`
- `FRONTEND_PHASE1_PLAN.md`
- `FRONTEND_FHIR_USAGE.md`
- `INTEGRATION_TASKS_PHASE1.md`
- `BACKEND_GAPS_FOR_PHASE1.md`
- `DOCKER.md`
- `docs/README.md`
- `docs/frontend/README.md`
