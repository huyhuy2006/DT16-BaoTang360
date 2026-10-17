#requires -Version 5.1
$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root
if (-not (Get-Command git -ErrorAction SilentlyContinue)) { throw 'Git không có trong PATH.' }
$top = git rev-parse --show-toplevel 2>$null
if ($LASTEXITCODE -ne 0) { throw 'Đây là source ZIP chưa có .git. Hãy mở đúng Git repository của nhóm; không giả lập lịch sử commit.' }
Write-Host "Repository: $top"
git status --short --branch
git diff --check
if ($LASTEXITCODE -ne 0) { throw 'git diff --check thất bại.' }
if (git status --porcelain) { throw 'Còn file thay đổi chưa được commit; chưa tạo tag v1.0.0.' }
if (git tag --list 'v1.0.0') { throw 'Tag v1.0.0 đã tồn tại; hãy xác minh tag, không ép ghi đè.' }
Write-Host 'Preflight đạt. Lệnh tạo tag phải được chạy thủ công sau khi xác minh nhánh main và kết quả test.' -ForegroundColor Green
