# FHIR Frontend（Phase 1）

本專案為 Laravel + Blade 前端應用，目標是以既有管理介面為基礎，完成 FHIR Phase 1 的可用整合。  
目前主軸是將患者與體溫紀錄流程對齊 FHIR 資源，並加強畫面在成功、錯誤、空資料與載入狀態下的可用性。

## 專案簡介

- 專案類型：Laravel Blade Web 前端（含後端控制器與 FHIR 串接邏輯）
- 主要功能：
  - 患者清單 / 建立 / 編輯
  - 體溫觀測（醫療紀錄）清單 / 建立 / 編輯
  - 依患者、依醫師分組檢視體溫觀測
- 整合方式：透過 Laravel 端 FHIR client 呼叫 FHIR server，前端頁面顯示轉換後的 ViewModel

## Phase 1 範圍

### In Scope

- `Patient` 資源流程（列表、建立、編輯）
- `Observation` 資源流程（僅體溫，LOINC `8310-5`）
- 前端狀態處理 hardening（loading / empty / error / success）
- 既有頁面結構下的功能調整與穩定化

### Out of Scope

- `Practitioner`（醫師）完整 FHIR 化重構
- `Condition` 正式臨床流程整合
- `Media` / `DocumentReference` 影像文件整合
- 大規模 UI 重新設計

## 技術棧

- PHP 8 + Laravel
- Blade Components
- Tailwind CSS
- Vite
- Alpine.js
- MySQL（開發環境可用 Docker Compose）

## 啟動方式

### 1) 本機開發（不透過 Docker）

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm run dev
```

預設服務：
- App: `http://localhost:8000`（`php artisan serve`）
- Vite: `http://localhost:5173`

### 2) Docker 開發

```bash
cp .env.docker .env
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d
docker compose exec app php artisan migrate --seed
```

預設服務：
- App: `http://localhost:8080`
- Vite: `http://localhost:5173`
- phpMyAdmin: `http://localhost:8081`

## 環境變數

Phase 1 前端 / 串接最重要的設定如下：

- `APP_URL`：應用對外網址
- `FHIR_PHASE1_ENABLED`：是否啟用 Phase 1 FHIR 流程（`true/false`）
- `FHIR_BASE_URL`：FHIR server base URL（例如 `http://localhost:8091/fhir`）
- `FHIR_TIMEOUT_SECONDS`：FHIR API timeout 秒數
- `FHIR_PATIENT_IDENTIFIER_SYSTEM`：Patient identifier system（預設 `urn:app:patient`）

範例（Docker）可參考 `.env.docker`。

## 後端串接方式

1. 前端頁面由 Controller 呼叫 `app/Services/Fhir/FhirApiClient.php`。
2. FHIR 回應透過 mapper / view model 轉為頁面可用資料：
   - `app/Support/Fhir/PatientMapper.php`
   - `app/Support/Fhir/ObservationMapper.php`
   - `app/ViewModels/PatientVM.php`
   - `app/ViewModels/TemperatureObservationVM.php`
3. 體溫 Observation 使用固定語意：
   - code: `8310-5`（Body temperature）
   - unit system: `http://unitsofmeasure.org`
   - unit code: `Cel`
4. 錯誤回應（包含 OperationOutcome）會轉換為可讀訊息，回到共用 alert / error 狀態元件顯示。

## 已知限制

- Phase 1 僅正式支援 `Patient` 與體溫 `Observation`。
- `kondisi` 目前僅視為過渡欄位，尚未落地為 `Condition` 正式流程。
- `picture` 尚未整合為 `Media` / `DocumentReference`。
- 醫師相關頁面仍有 legacy 成分，尚非完整 `Practitioner` 對應。
- `FHIR_PHASE1_ENABLED=false` 的完整 legacy fallback 仍在後續補齊中。

## 文件索引

- [FRONTEND_FHIR_USAGE.md](FRONTEND_FHIR_USAGE.md)：前端欄位與 FHIR 映射盤點
- [FRONTEND_PHASE1_PLAN.md](FRONTEND_PHASE1_PLAN.md)：Phase 1 計畫與執行狀態
- [INTEGRATION_TASKS_PHASE1.md](INTEGRATION_TASKS_PHASE1.md)：整合任務列表
- [BACKEND_GAPS_FOR_PHASE1.md](BACKEND_GAPS_FOR_PHASE1.md)：後端缺口整理
- [SERVER_CAPABILITY.md](SERVER_CAPABILITY.md)：伺服器能力與約束
- [DOCKER.md](DOCKER.md)：Docker 開發指引
- [docs/README.md](docs/README.md)：文件總覽
- [docs/frontend/README.md](docs/frontend/README.md)：前端文件入口
- [docs/frontend/commit-checklist.md](docs/frontend/commit-checklist.md)：前端提交檢查清單
