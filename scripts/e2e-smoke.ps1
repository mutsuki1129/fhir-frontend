param(
    [string]$BaseUrl = "http://localhost:8080",
    [string]$ImagePath = "public/images/logo.png"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Write-Step {
    param([string]$Message)
    Write-Host "[STEP] $Message" -ForegroundColor Cyan
}

function Write-Pass {
    param([string]$Message)
    Write-Host "[PASS] $Message" -ForegroundColor Green
}

function Fail {
    param([string]$Message)
    Write-Host "[FAIL] $Message" -ForegroundColor Red
    exit 1
}

function Get-CsrfToken {
    param([string]$Html)
    $match = [regex]::Match($Html, 'name="_token" value="([^"]+)"')
    if (-not $match.Success) {
        Fail "Cannot find CSRF token in response HTML."
    }
    return $match.Groups[1].Value
}

function New-RandomEmail {
    param([string]$Prefix)
    $stamp = Get-Date -Format "yyyyMMddHHmmssfff"
    return "${Prefix}_${stamp}@example.com"
}

function Assert-Status200 {
    param(
        [string]$Url,
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session
    )

    try {
        $res = Invoke-WebRequest -Uri $Url -WebSession $Session -UseBasicParsing -TimeoutSec 30
        if ($res.StatusCode -ne 200) {
            Fail "Expected 200 for $Url, got $($res.StatusCode)."
        }
    }
    catch {
        Fail "Request failed for $Url. $($_.Exception.Message)"
    }
}

function Get-CookieHeader {
    param(
        [string]$Url,
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session
    )
    return (($Session.Cookies.GetCookies($Url) | ForEach-Object { "$($_.Name)=$($_.Value)" }) -join "; ")
}

function Curl-MultipartStatus {
    param(
        [string]$Url,
        [string[]]$Forms,
        [string]$CookieHeader
    )

    $args = @("-s", "-o", "NUL", "-w", "%{http_code}", "-X", "POST", $Url, "-H", "Cookie: $CookieHeader")
    foreach ($form in $Forms) {
        $args += @("-F", $form)
    }

    $status = (& curl.exe @args).Trim()
    return $status
}

Write-Step "Checking required file: $ImagePath"
if (-not (Test-Path -LiteralPath $ImagePath)) {
    Fail "Image file not found at '$ImagePath'."
}
$imageFullPath = (Resolve-Path -LiteralPath $ImagePath).Path

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

Write-Step "Basic guest page checks"
Assert-Status200 -Url "$BaseUrl/" -Session $session
Assert-Status200 -Url "$BaseUrl/login" -Session $session
Assert-Status200 -Url "$BaseUrl/register" -Session $session
Write-Pass "Guest pages are reachable"

Write-Step "Registering a fresh user"
$registerPage = Invoke-WebRequest -Uri "$BaseUrl/register" -WebSession $session -UseBasicParsing
$registerToken = Get-CsrfToken -Html $registerPage.Content
$testEmail = New-RandomEmail -Prefix "e2e_user"
$testPassword = "Password123!"
$registered = Invoke-WebRequest -Uri "$BaseUrl/register" -Method Post -Body @{
    _token = $registerToken
    name = "E2E Smoke User"
    email = $testEmail
    password = $testPassword
    password_confirmation = $testPassword
} -WebSession $session -UseBasicParsing
if ($registered.BaseResponse.ResponseUri.AbsoluteUri -notlike "*dashboard*") {
    Fail "Registration did not end on dashboard."
}
Write-Pass "Registration and auto-login succeeded ($testEmail)"

Write-Step "Finding existing dokter/pasien IDs"
$dokterList = Invoke-WebRequest -Uri "$BaseUrl/dokters" -WebSession $session -UseBasicParsing
$pasienList = Invoke-WebRequest -Uri "$BaseUrl/pasiens" -WebSession $session -UseBasicParsing
$dokterIdMatch = [regex]::Match($dokterList.Content, "/edit-dokter/(\d+)")
$pasienIdMatch = [regex]::Match($pasienList.Content, "/edit-pasien/(\d+)")
if (-not $dokterIdMatch.Success) { Fail "Cannot find any dokter id from /dokters." }
if (-not $pasienIdMatch.Success) { Fail "Cannot find any pasien id from /pasiens." }
$dokterId = $dokterIdMatch.Groups[1].Value
$pasienId = $pasienIdMatch.Groups[1].Value
Write-Pass "Found dokter id=$dokterId and pasien id=$pasienId"

$cookieHeader = Get-CookieHeader -Url $BaseUrl -Session $session

Write-Step "Uploading dokter profile picture"
$dokterEdit = Invoke-WebRequest -Uri "$BaseUrl/edit-dokter/$dokterId" -WebSession $session -UseBasicParsing
$dokterToken = Get-CsrfToken -Html $dokterEdit.Content
$dokterUploadStatus = Curl-MultipartStatus -Url "$BaseUrl/photo-dokter/$dokterId" -CookieHeader $cookieHeader -Forms @(
    "_token=$dokterToken",
    "_method=patch",
    "type=update",
    "profile_picture=@$imageFullPath;type=image/png"
)
if ($dokterUploadStatus -ne "302") {
    Fail "Dokter photo upload expected 302, got $dokterUploadStatus."
}
Write-Pass "Dokter profile picture upload succeeded"

Write-Step "Uploading pasien profile picture"
$pasienEdit = Invoke-WebRequest -Uri "$BaseUrl/edit-pasien/$pasienId" -WebSession $session -UseBasicParsing
$pasienToken = Get-CsrfToken -Html $pasienEdit.Content
$pasienUploadStatus = Curl-MultipartStatus -Url "$BaseUrl/photo-pasien/$pasienId" -CookieHeader $cookieHeader -Forms @(
    "_token=$pasienToken",
    "_method=patch",
    "type=update",
    "profile_picture=@$imageFullPath;type=image/png"
)
if ($pasienUploadStatus -ne "302") {
    Fail "Pasien photo upload expected 302, got $pasienUploadStatus."
}
Write-Pass "Pasien profile picture upload succeeded"

Write-Step "Creating rekam with image upload"
$rekamCreatePage = Invoke-WebRequest -Uri "$BaseUrl/rekam/create" -WebSession $session -UseBasicParsing
$rekamToken = Get-CsrfToken -Html $rekamCreatePage.Content
$rekamCreateStatus = Curl-MultipartStatus -Url "$BaseUrl/rekam" -CookieHeader $cookieHeader -Forms @(
    "_token=$rekamToken",
    "pasien=$pasienId",
    "dokter=$dokterId",
    "kondisi=E2E Smoke Rekam",
    "suhu=37.2",
    "picture=@$imageFullPath;type=image/png"
)
if ($rekamCreateStatus -ne "302") {
    Fail "Rekam create expected 302, got $rekamCreateStatus."
}
Write-Pass "Rekam create with image upload succeeded"

Write-Step "Testing dokter delete flow"
$createDokterPage = Invoke-WebRequest -Uri "$BaseUrl/dokters/create" -WebSession $session -UseBasicParsing
$createDokterToken = Get-CsrfToken -Html $createDokterPage.Content
$deleteDokterEmail = New-RandomEmail -Prefix "delete_dokter"
Invoke-WebRequest -Uri "$BaseUrl/dokters" -Method Post -Body @{
    _token = $createDokterToken
    name = "Delete Dokter"
    email = $deleteDokterEmail
    phone_number = "081200000001"
    role_id = "1"
    password = "secret12"
    age = "39"
    height = "169"
    weight = "67"
} -WebSession $session -UseBasicParsing | Out-Null
$dokterSearchBefore = Invoke-WebRequest -Uri "$BaseUrl/dokters?query=$deleteDokterEmail" -WebSession $session -UseBasicParsing
$deleteDokterIdMatch = [regex]::Match($dokterSearchBefore.Content, "/edit-dokter/(\d+)")
if (-not $deleteDokterIdMatch.Success) { Fail "Cannot find created dokter id for delete test." }
$deleteDokterId = $deleteDokterIdMatch.Groups[1].Value
Invoke-WebRequest -Uri "$BaseUrl/delete-dokter/$deleteDokterId" -WebSession $session -UseBasicParsing | Out-Null
$dokterSearchAfter = Invoke-WebRequest -Uri "$BaseUrl/dokters?query=$deleteDokterEmail" -WebSession $session -UseBasicParsing
if ($dokterSearchAfter.Content -match "/edit-dokter/$deleteDokterId") {
    Fail "Dokter delete flow failed. id=$deleteDokterId still exists."
}
Write-Pass "Dokter delete flow succeeded"

Write-Step "Testing pasien delete flow"
$createPasienPage = Invoke-WebRequest -Uri "$BaseUrl/pasiens/create" -WebSession $session -UseBasicParsing
$createPasienToken = Get-CsrfToken -Html $createPasienPage.Content
$deletePasienEmail = New-RandomEmail -Prefix "delete_pasien"
Invoke-WebRequest -Uri "$BaseUrl/pasiens" -Method Post -Body @{
    _token = $createPasienToken
    name = "Delete Pasien"
    email = $deletePasienEmail
    phone_number = "089200000001"
    role_id = "2"
    password = "secret12"
    age = "29"
    height = "160"
    weight = "54"
} -WebSession $session -UseBasicParsing | Out-Null
$pasienSearchBefore = Invoke-WebRequest -Uri "$BaseUrl/pasiens?query=$deletePasienEmail" -WebSession $session -UseBasicParsing
$deletePasienIdMatch = [regex]::Match($pasienSearchBefore.Content, "/edit-pasien/(\d+)")
if (-not $deletePasienIdMatch.Success) { Fail "Cannot find created pasien id for delete test." }
$deletePasienId = $deletePasienIdMatch.Groups[1].Value
Invoke-WebRequest -Uri "$BaseUrl/delete-pasien/$deletePasienId" -WebSession $session -UseBasicParsing | Out-Null
$pasienSearchAfter = Invoke-WebRequest -Uri "$BaseUrl/pasiens?query=$deletePasienEmail" -WebSession $session -UseBasicParsing
if ($pasienSearchAfter.Content -match "/edit-pasien/$deletePasienId") {
    Fail "Pasien delete flow failed. id=$deletePasienId still exists."
}
Write-Pass "Pasien delete flow succeeded"

Write-Step "Testing logout and login again"
$dashboardPage = Invoke-WebRequest -Uri "$BaseUrl/dashboard" -WebSession $session -UseBasicParsing
$logoutToken = Get-CsrfToken -Html $dashboardPage.Content
Invoke-WebRequest -Uri "$BaseUrl/logout" -Method Post -Body @{ _token = $logoutToken } -WebSession $session -UseBasicParsing | Out-Null
$afterLogout = Invoke-WebRequest -Uri "$BaseUrl/dashboard" -WebSession $session -UseBasicParsing
if ($afterLogout.BaseResponse.ResponseUri.AbsoluteUri -notlike "*login*") {
    Fail "After logout, /dashboard should redirect to login."
}
$loginPage = Invoke-WebRequest -Uri "$BaseUrl/login" -WebSession $session -UseBasicParsing
$loginToken = Get-CsrfToken -Html $loginPage.Content
$afterLogin = Invoke-WebRequest -Uri "$BaseUrl/login" -Method Post -Body @{
    _token = $loginToken
    email = $testEmail
    password = $testPassword
} -WebSession $session -UseBasicParsing
if ($afterLogin.BaseResponse.ResponseUri.AbsoluteUri -notlike "*dashboard*") {
    Fail "Re-login did not end on dashboard."
}
Write-Pass "Logout/login flow succeeded"

Write-Step "Final protected page smoke checks"
$protected = @(
    "/dashboard",
    "/dokters",
    "/dokters/create",
    "/edit-dokter/$dokterId",
    "/pasiens",
    "/pasiens/create",
    "/edit-pasien/$pasienId",
    "/rekam",
    "/rekam/create",
    "/rekam/pasien",
    "/rekam/dokter",
    "/profile"
)
foreach ($path in $protected) {
    Assert-Status200 -Url "$BaseUrl$path" -Session $session
}
Write-Pass "Protected page smoke checks passed"

Write-Host ""
Write-Host "E2E smoke test completed successfully." -ForegroundColor Green
exit 0
