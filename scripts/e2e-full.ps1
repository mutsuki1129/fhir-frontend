param(
    [string]$BaseUrl = "http://localhost:8080",
    [string]$ImagePath = "public/images/logo.png"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Step([string]$message) { Write-Host "[STEP] $message" -ForegroundColor Cyan }
function Pass([string]$message) { Write-Host "[PASS] $message" -ForegroundColor Green }
function Fail([string]$message) { Write-Host "[FAIL] $message" -ForegroundColor Red; exit 1 }

function Assert($condition, [string]$message) {
    if (-not $condition) { Fail $message }
}

function Get-CsrfToken([string]$html) {
    $m = [regex]::Match($html, 'name="_token" value="([^"]+)"')
    if (-not $m.Success) { Fail "CSRF token not found." }
    $m.Groups[1].Value
}

function New-Mail([string]$prefix) {
    $stamp = Get-Date -Format "yyyyMMddHHmmssfff"
    "${prefix}_${stamp}@example.com"
}

function Http200([string]$url, [Microsoft.PowerShell.Commands.WebRequestSession]$session) {
    try {
        $r = Invoke-WebRequest -Uri $url -WebSession $session -UseBasicParsing -TimeoutSec 30
        if ($r.StatusCode -ne 200) {
            Fail "Expected 200 for $url, got $($r.StatusCode)."
        }
        $r
    } catch {
        Fail "Request failed: $url :: $($_.Exception.Message)"
    }
}

function CookieHeader([string]$baseUrl, [Microsoft.PowerShell.Commands.WebRequestSession]$session) {
    (($session.Cookies.GetCookies($baseUrl) | ForEach-Object { "$($_.Name)=$($_.Value)" }) -join "; ")
}

function CurlMultipartStatus([string]$url, [string]$cookieHeader, [string[]]$forms) {
    $args = @("-s", "-o", "NUL", "-w", "%{http_code}", "-X", "POST", $url, "-H", "Cookie: $cookieHeader")
    foreach ($f in $forms) { $args += @("-F", $f) }
    (& curl.exe @args).Trim()
}

if (-not (Test-Path -LiteralPath $ImagePath)) {
    Fail "Image file not found at '$ImagePath'."
}
$imageAbs = (Resolve-Path -LiteralPath $ImagePath).Path

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

Step "Guest pages smoke"
$null = Http200 "$BaseUrl/" $session
$null = Http200 "$BaseUrl/login" $session
$null = Http200 "$BaseUrl/register" $session
Pass "Guest pages OK"

Step "Register and login"
$register = Http200 "$BaseUrl/register" $session
$regToken = Get-CsrfToken $register.Content
$email = New-Mail "full_e2e_user"
$password = "Password123!"
$afterRegister = Invoke-WebRequest -Uri "$BaseUrl/register" -Method Post -Body @{
    _token = $regToken
    name = "Full E2E User"
    email = $email
    password = $password
    password_confirmation = $password
} -WebSession $session -UseBasicParsing
Assert ($afterRegister.BaseResponse.ResponseUri.AbsoluteUri -like "*dashboard*") "Register should redirect to dashboard."
Pass "Register OK: $email"

Step "Dokters list, query, sort"
$null = Http200 "$BaseUrl/dokters" $session
$null = Http200 "$BaseUrl/dokters?query=a" $session
$null = Http200 "$BaseUrl/dokters?sort_by=name_desc" $session
Pass "Dokters listing/query/sort OK"

Step "Create dokter"
$dokCreate = Http200 "$BaseUrl/dokters/create" $session
$dokCreateToken = Get-CsrfToken $dokCreate.Content
$dokEmail = New-Mail "full_e2e_dok"
$dokCreateResp = Invoke-WebRequest -Uri "$BaseUrl/dokters" -Method Post -Body @{
    _token = $dokCreateToken
    name = "Dokter Full"
    email = $dokEmail
    phone_number = "081234560001"
    role_id = "1"
    password = "secret12"
    age = "40"
    height = "170"
    weight = "70"
} -WebSession $session -UseBasicParsing
Assert ($dokCreateResp.BaseResponse.ResponseUri.AbsoluteUri -like "*dokters*") "Create dokter should redirect to /dokters."
$dokSearch = Http200 "$BaseUrl/dokters?query=$dokEmail" $session
$dokIdMatch = [regex]::Match($dokSearch.Content, "/edit-dokter/(\d+)")
Assert $dokIdMatch.Success "Cannot find created dokter id."
$dokId = $dokIdMatch.Groups[1].Value
Pass "Create dokter OK (id=$dokId)"

Step "Update dokter"
$dokEdit = Http200 "$BaseUrl/edit-dokter/$dokId" $session
$dokEditToken = Get-CsrfToken $dokEdit.Content
$dokEmailUpdated = New-Mail "full_e2e_dok_upd"
$dokUpdateResp = Invoke-WebRequest -Uri "$BaseUrl/edit-dokter/$dokId" -Method Post -Body @{
    _token = $dokEditToken
    _method = "PATCH"
    name = "Dokter Full Updated"
    email = $dokEmailUpdated
    phone_number = "081234560002"
    role_id = "1"
    age = "41"
    height = "171"
    weight = "71"
} -WebSession $session -UseBasicParsing
Assert ($dokUpdateResp.BaseResponse.ResponseUri.AbsoluteUri -like "*dokters*") "Update dokter should redirect to /dokters."
$dokSearchUpdated = Http200 "$BaseUrl/dokters?query=$dokEmailUpdated" $session
Assert ($dokSearchUpdated.Content -match [regex]::Escape($dokEmailUpdated)) "Updated dokter email not found in list."
Pass "Update dokter OK"

Step "Dokter photo upload + delete"
$dokPhotoPage = Http200 "$BaseUrl/edit-dokter/$dokId" $session
$dokPhotoToken = Get-CsrfToken $dokPhotoPage.Content
$cookieHeader = CookieHeader $BaseUrl $session
$dokUploadCode = CurlMultipartStatus "$BaseUrl/photo-dokter/$dokId" $cookieHeader @(
    "_token=$dokPhotoToken",
    "_method=patch",
    "type=update",
    "profile_picture=@$imageAbs;type=image/png"
)
Assert ($dokUploadCode -eq "302") "Dokter photo upload expected 302, got $dokUploadCode."
$dokPhotoPage2 = Http200 "$BaseUrl/edit-dokter/$dokId" $session
$dokPhotoToken2 = Get-CsrfToken $dokPhotoPage2.Content
$dokDeletePicCode = CurlMultipartStatus "$BaseUrl/photo-dokter/$dokId" $cookieHeader @(
    "_token=$dokPhotoToken2",
    "_method=patch",
    "type=delete"
)
Assert ($dokDeletePicCode -eq "302") "Dokter photo delete expected 302, got $dokDeletePicCode."
Pass "Dokter photo upload/delete OK"

Step "Pasiens list, query, sort"
$null = Http200 "$BaseUrl/pasiens" $session
$null = Http200 "$BaseUrl/pasiens?query=a" $session
$null = Http200 "$BaseUrl/pasiens?sort_by=age_desc" $session
Pass "Pasiens listing/query/sort OK"

Step "Create pasien"
$pasCreate = Http200 "$BaseUrl/pasiens/create" $session
$pasCreateToken = Get-CsrfToken $pasCreate.Content
$pasEmail = New-Mail "full_e2e_pas"
$pasCreateResp = Invoke-WebRequest -Uri "$BaseUrl/pasiens" -Method Post -Body @{
    _token = $pasCreateToken
    name = "Pasien Full"
    email = $pasEmail
    phone_number = "089876540001"
    role_id = "2"
    password = "secret12"
    age = "30"
    height = "160"
    weight = "55"
} -WebSession $session -UseBasicParsing
Assert ($pasCreateResp.BaseResponse.ResponseUri.AbsoluteUri -like "*pasiens*") "Create pasien should redirect to /pasiens."
$pasSearch = Http200 "$BaseUrl/pasiens?query=$pasEmail" $session
$pasIdMatch = [regex]::Match($pasSearch.Content, "/edit-pasien/(\d+)")
Assert $pasIdMatch.Success "Cannot find created pasien id."
$pasId = $pasIdMatch.Groups[1].Value
Pass "Create pasien OK (id=$pasId)"

Step "Update pasien"
$pasEdit = Http200 "$BaseUrl/edit-pasien/$pasId" $session
$pasEditToken = Get-CsrfToken $pasEdit.Content
$pasEmailUpdated = New-Mail "full_e2e_pas_upd"
$pasUpdateResp = Invoke-WebRequest -Uri "$BaseUrl/edit-pasien/$pasId" -Method Post -Body @{
    _token = $pasEditToken
    _method = "PATCH"
    name = "Pasien Full Updated"
    email = $pasEmailUpdated
    phone_number = "089876540002"
    role_id = "2"
    age = "31"
    height = "161"
    weight = "56"
} -WebSession $session -UseBasicParsing
Assert ($pasUpdateResp.BaseResponse.ResponseUri.AbsoluteUri -like "*pasiens*") "Update pasien should redirect to /pasiens."
$pasSearchUpdated = Http200 "$BaseUrl/pasiens?query=$pasEmailUpdated" $session
Assert ($pasSearchUpdated.Content -match [regex]::Escape($pasEmailUpdated)) "Updated pasien email not found in list."
Pass "Update pasien OK"

Step "Pasien photo upload + delete"
$pasPhotoPage = Http200 "$BaseUrl/edit-pasien/$pasId" $session
$pasPhotoToken = Get-CsrfToken $pasPhotoPage.Content
$pasUploadCode = CurlMultipartStatus "$BaseUrl/photo-pasien/$pasId" $cookieHeader @(
    "_token=$pasPhotoToken",
    "_method=patch",
    "type=update",
    "profile_picture=@$imageAbs;type=image/png"
)
Assert ($pasUploadCode -eq "302") "Pasien photo upload expected 302, got $pasUploadCode."
$pasPhotoPage2 = Http200 "$BaseUrl/edit-pasien/$pasId" $session
$pasPhotoToken2 = Get-CsrfToken $pasPhotoPage2.Content
$pasDeletePicCode = CurlMultipartStatus "$BaseUrl/photo-pasien/$pasId" $cookieHeader @(
    "_token=$pasPhotoToken2",
    "_method=patch",
    "type=delete"
)
Assert ($pasDeletePicCode -eq "302") "Pasien photo delete expected 302, got $pasDeletePicCode."
Pass "Pasien photo upload/delete OK"

Step "Rekam create/list/group"
$null = Http200 "$BaseUrl/rekam" $session
$null = Http200 "$BaseUrl/rekam/pasien" $session
$null = Http200 "$BaseUrl/rekam/dokter" $session
$rekCreate = Http200 "$BaseUrl/rekam/create" $session
$rekCreateToken = Get-CsrfToken $rekCreate.Content
$rekCreateCode = CurlMultipartStatus "$BaseUrl/rekam" $cookieHeader @(
    "_token=$rekCreateToken",
    "pasien=$pasId",
    "dokter=$dokId",
    "kondisi=Full E2E Rekam Initial",
    "suhu=37.1",
    "picture=@$imageAbs;type=image/png"
)
Assert ($rekCreateCode -eq "302") "Rekam create expected 302, got $rekCreateCode."
$rekList = Http200 "$BaseUrl/rekam" $session
$rekIdMatch = [regex]::Match($rekList.Content, "/rekam/(\d+)/edit")
Assert $rekIdMatch.Success "Cannot find rekam id in list."
$rekId = $rekIdMatch.Groups[1].Value
Pass "Create rekam OK (id=$rekId)"

Step "Rekam update (verify no disappearing bug)"
$rekEdit = Http200 "$BaseUrl/rekam/$rekId/edit" $session
$rekEditToken = Get-CsrfToken $rekEdit.Content
$updatedKondisi = "Full E2E Rekam Updated"
$rekUpdateResp = Invoke-WebRequest -Uri "$BaseUrl/rekam/$rekId" -Method Post -Body @{
    _token = $rekEditToken
    _method = "PATCH"
    pasien = $pasId
    dokter = $dokId
    kondisi = $updatedKondisi
    suhu = "37.4"
} -WebSession $session -UseBasicParsing
Assert ($rekUpdateResp.BaseResponse.ResponseUri.AbsoluteUri -like "*rekam*") "Rekam update should redirect to /rekam."
$rekListAfterUpdate = Http200 "$BaseUrl/rekam" $session
Assert ($rekListAfterUpdate.Content -match [regex]::Escape($updatedKondisi)) "Updated rekam not found in list (disappearing bug)."
$rekByPas = Http200 "$BaseUrl/rekam/pasien" $session
Assert ($rekByPas.Content -match [regex]::Escape($updatedKondisi)) "Updated rekam missing in grouped-by-pasien page."
$rekByDok = Http200 "$BaseUrl/rekam/dokter" $session
Assert ($rekByDok.Content -match [regex]::Escape($updatedKondisi)) "Updated rekam missing in grouped-by-dokter page."
Pass "Rekam update keeps data visible"

Step "Rekam delete"
$rekDeletePage = Http200 "$BaseUrl/rekam" $session
$rekDeleteToken = Get-CsrfToken $rekDeletePage.Content
Invoke-WebRequest -Uri "$BaseUrl/rekam/$rekId" -Method Post -Body @{
    _token = $rekDeleteToken
    _method = "DELETE"
} -WebSession $session -UseBasicParsing | Out-Null
$rekListAfterDelete = Http200 "$BaseUrl/rekam" $session
Assert (-not ($rekListAfterDelete.Content -match "/rekam/$rekId/edit")) "Deleted rekam still present in list."
Pass "Rekam delete OK"

Step "Dokter/Pasien delete"
Invoke-WebRequest -Uri "$BaseUrl/delete-dokter/$dokId" -WebSession $session -UseBasicParsing | Out-Null
Invoke-WebRequest -Uri "$BaseUrl/delete-pasien/$pasId" -WebSession $session -UseBasicParsing | Out-Null
$dokAfterDelete = Http200 "$BaseUrl/dokters?query=$dokEmailUpdated" $session
$pasAfterDelete = Http200 "$BaseUrl/pasiens?query=$pasEmailUpdated" $session
Assert (-not ($dokAfterDelete.Content -match "/edit-dokter/$dokId")) "Deleted dokter still appears."
Assert (-not ($pasAfterDelete.Content -match "/edit-pasien/$pasId")) "Deleted pasien still appears."
Pass "Dokter/Pasien delete OK"

Step "Logout and relogin"
$dashboard = Http200 "$BaseUrl/dashboard" $session
$logoutToken = Get-CsrfToken $dashboard.Content
Invoke-WebRequest -Uri "$BaseUrl/logout" -Method Post -Body @{ _token = $logoutToken } -WebSession $session -UseBasicParsing | Out-Null
$afterLogout = Http200 "$BaseUrl/dashboard" $session
Assert ($afterLogout.BaseResponse.ResponseUri.AbsoluteUri -like "*login*") "After logout, dashboard should redirect to login."
$loginPage = Http200 "$BaseUrl/login" $session
$loginToken = Get-CsrfToken $loginPage.Content
$afterLogin = Invoke-WebRequest -Uri "$BaseUrl/login" -Method Post -Body @{
    _token = $loginToken
    email = $email
    password = $password
} -WebSession $session -UseBasicParsing
Assert ($afterLogin.BaseResponse.ResponseUri.AbsoluteUri -like "*dashboard*") "Relogin should redirect to dashboard."
Pass "Logout/login flow OK"

Write-Host ""
Write-Host "E2E full test completed successfully." -ForegroundColor Green
exit 0
