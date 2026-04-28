param(
    [string]$BaseUrl = "http://localhost:8080",
    [string]$FhirBaseUrl = "http://localhost:8091/fhir"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Step([string]$message) { Write-Host "[STEP] $message" -ForegroundColor Cyan }
function Pass([string]$message) { Write-Host "[PASS] $message" -ForegroundColor Green }
function Fail([string]$message) { Write-Host "[FAIL] $message" -ForegroundColor Red; exit 1 }

function Get-CsrfToken([string]$html) {
    $m = [regex]::Match($html, 'name="_token" value="([^"]+)"')
    if (-not $m.Success) { Fail "CSRF token not found." }
    return $m.Groups[1].Value
}

function New-Mail([string]$prefix) {
    $stamp = Get-Date -Format "yyyyMMddHHmmssfff"
    return "${prefix}_${stamp}@example.com"
}

Step "Register and login test user"
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$registerPage = Invoke-WebRequest -Uri "$BaseUrl/register" -WebSession $session -UseBasicParsing
$registerToken = Get-CsrfToken $registerPage.Content
$email = New-Mail "phase2_cond"
$password = "Password123!"
$afterRegister = Invoke-WebRequest -Uri "$BaseUrl/register" -Method Post -Body @{
    _token = $registerToken
    name = "Phase2 Condition Smoke"
    email = $email
    password = $password
    password_confirmation = $password
} -WebSession $session -UseBasicParsing
if ($afterRegister.BaseResponse.ResponseUri.AbsoluteUri -notlike "*dashboard*") {
    Fail "Register/login did not land on dashboard."
}
Pass "Registered and logged in"

Step "Pick one patient from FHIR"
$patientBundle = Invoke-RestMethod -Uri "$FhirBaseUrl/Patient?_count=1" -Method Get
$patientId = [string](@($patientBundle.entry)[0].resource.id)
if (-not $patientId) { Fail "No patient found in FHIR server." }
Pass "Using patient id=$patientId"

$stamp = Get-Date -Format "yyyyMMddHHmmssfff"
$initialConditionText = "phase2-initial-$stamp"
$updatedConditionText = "phase2-updated-$stamp"
$legacyNote = "legacy-$stamp"

Step "Create rekam with condition fields"
$createPage = Invoke-WebRequest -Uri "$BaseUrl/rekam/create" -WebSession $session -UseBasicParsing
$createToken = Get-CsrfToken $createPage.Content
Invoke-WebRequest -Uri "$BaseUrl/rekam" -Method Post -Body @{
    _token = $createToken
    pasien = $patientId
    suhu = "37.2"
    effective_datetime = "2026-04-28T12:30"
    kondisi = $legacyNote
    condition_code = "phase2-smoke"
    condition_text = $initialConditionText
} -WebSession $session -UseBasicParsing | Out-Null
Pass "Create request submitted"

Step "Find created observation id by unique legacy note"
$obsBundle = Invoke-RestMethod -Uri "$FhirBaseUrl/Observation?code=8310-5&_count=200" -Method Get
$observationId = $null
foreach ($entry in @($obsBundle.entry)) {
    $res = $entry.resource
    if (-not $res) { continue }
    $note = [string]$res.note[0].text
    if ($note -eq $legacyNote) {
        $observationId = [string]$res.id
        break
    }
}
if (-not $observationId) { Fail "Cannot locate created observation by legacy note." }
Pass "Found observation id=$observationId"

Step "Update condition text on the same rekam"
$editPage = Invoke-WebRequest -Uri "$BaseUrl/rekam/$observationId/edit" -WebSession $session -UseBasicParsing
$editToken = Get-CsrfToken $editPage.Content
$conditionIdMatch = [regex]::Match($editPage.Content, 'name="condition_id" value="([^"]*)"')
$conditionId = ""
if ($conditionIdMatch.Success) { $conditionId = $conditionIdMatch.Groups[1].Value }

Invoke-WebRequest -Uri "$BaseUrl/rekam/$observationId" -Method Post -Body @{
    _token = $editToken
    _method = "PATCH"
    pasien = $patientId
    suhu = "37.4"
    effective_datetime = "2026-04-28T13:00"
    kondisi = $legacyNote
    condition_id = $conditionId
    condition_code = "phase2-smoke"
    condition_text = $updatedConditionText
} -WebSession $session -UseBasicParsing | Out-Null
Pass "Update request submitted"

Step "Assert /rekam list shows updated condition and legacy fallback content"
$listPage = Invoke-WebRequest -Uri "$BaseUrl/rekam" -WebSession $session -UseBasicParsing
$html = $listPage.Content
if ($html -notmatch [regex]::Escape($updatedConditionText)) {
    Fail "Updated condition text not found in /rekam list."
}
if ($html -notmatch [regex]::Escape($legacyNote)) {
    Fail "Legacy note not found in /rekam list."
}
Pass "List shows condition + legacy content"

Write-Host ""
Write-Host "Phase 2 condition smoke test completed successfully." -ForegroundColor Green
exit 0
