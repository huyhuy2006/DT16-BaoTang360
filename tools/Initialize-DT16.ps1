#requires -Version 5.1
<##
.SYNOPSIS
  Khởi tạo cấu hình Docker Compose cục bộ cho DT-16 mà không ghi đè .env đang có.
.DESCRIPTION
  Chỉ dùng trên máy cá nhân. Không dùng file sinh ra ở production.
  Sinh APP_KEY, mật khẩu MySQL và token Python mới; không ghi đè cấu hình đã có.
##>
$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $PSScriptRoot
$Example = Join-Path $Root '.env.example'
$EnvFile = Join-Path $Root '.env'

if (Test-Path -LiteralPath $EnvFile) {
    Write-Host 'Đã có .env. Không ghi đè bất kỳ cấu hình nào.' -ForegroundColor Yellow
    Write-Host 'Kiểm tra thủ công bằng trình soạn thảo an toàn; không dán nội dung file vào chat.'
    exit 0
}
if (-not (Test-Path -LiteralPath $Example)) { throw 'Không tìm thấy .env.example ở thư mục gốc.' }

$Bytes = New-Object byte[] 32
$RandomGenerator = [System.Security.Cryptography.RandomNumberGenerator]::Create()
try { $RandomGenerator.GetBytes($Bytes) } finally { $RandomGenerator.Dispose() }
$AppKey = 'base64:' + [Convert]::ToBase64String($Bytes)
$DbPassword = [guid]::NewGuid().ToString('N') + 'A9!'
$RootPassword = [guid]::NewGuid().ToString('N') + 'R7!'
$PythonToken = [guid]::NewGuid().ToString('N') + [guid]::NewGuid().ToString('N')
$Content = Get-Content -LiteralPath $Example -Raw -Encoding UTF8
$Content = $Content.Replace('APP_KEY=', "APP_KEY=$AppKey")
$Content = $Content.Replace('MYSQL_PASSWORD=CHANGE_ME_LOCAL_ONLY', "MYSQL_PASSWORD=$DbPassword")
$Content = $Content.Replace('DB_PASSWORD=CHANGE_ME_LOCAL_ONLY', "DB_PASSWORD=$DbPassword")
$Content = $Content.Replace('MYSQL_ROOT_PASSWORD=CHANGE_ME_ROOT_LOCAL_ONLY', "MYSQL_ROOT_PASSWORD=$RootPassword")
$Content = $Content.Replace('PYTHON_SERVICE_TOKEN=CHANGE_ME_RANDOM_TOKEN', "PYTHON_SERVICE_TOKEN=$PythonToken")
# .NET UTF8Encoding($false) makes a UTF-8 file without BOM.
$Utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($EnvFile, $Content.TrimEnd("`r", "`n") + "`n", $Utf8NoBom)
Write-Host 'Đã tạo .env cục bộ với khóa/mật khẩu ngẫu nhiên.' -ForegroundColor Green
Write-Host 'Không mở rộng nội dung .env ra log, ảnh chụp hoặc Git.'
Write-Host 'Đây là cấu hình local; production phải dùng bí mật riêng và APP_DEBUG=false.'
