#requires -Version 5.1
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Write-Host '== DT-16 source verification ==' -ForegroundColor Cyan

$csv = Join-Path $root 'database\raw\dt16_300_records.csv'
if (-not (Test-Path -LiteralPath $csv)) { throw "Missing dataset: $csv" }
$rows = (Import-Csv -LiteralPath $csv).Count
if ($rows -ne 300) { throw "CSV records = $rows, expected 300" }
Write-Host "[PASS] CSV data rows: $rows" -ForegroundColor Green

$matrix = Join-Path $root '05_KiemThu\60-ca-kiem-thu.csv'
$caseData = @(Import-Csv -LiteralPath $matrix)
$caseRows = $caseData.Count
if ($caseRows -ne 60) { throw "Test case rows = $caseRows, expected 60" }
if ($caseData[0].PSObject.Properties.Count -ne 13) { throw "Test matrix has $($caseData[0].PSObject.Properties.Count) columns, expected 13" }
Write-Host "[PASS] Manual test matrix rows: $caseRows" -ForegroundColor Green

$phpTestCount = (Get-ChildItem (Join-Path $root 'backend\tests') -Recurse -Filter *.php | ForEach-Object { Select-String -Path $_.FullName -Pattern 'function test_' }).Count
$pwTestCount = (Get-ChildItem (Join-Path $root 'frontend\tests\e2e') -Filter *.ts | ForEach-Object { Select-String -Path $_.FullName -Pattern '\btest\(' }).Count
if (($phpTestCount + $pwTestCount) -ne 60) { throw "Automated test definitions = $($phpTestCount + $pwTestCount), expected 60 (PHPUnit $phpTestCount; Playwright $pwTestCount)" }
Write-Host "[PASS] Automated test definitions: PHPUnit=$phpTestCount + Playwright=$pwTestCount = 60" -ForegroundColor Green

$bomFiles = @()
$backupFiles = @()
$envLeaks = @()
$extensions = @('.php','.js','.ts','.vue','.py','.json','.md','.sql','.css','.html','.yml','.yaml','.ps1','.xml','.txt','.csv','.svg','.example')
foreach ($file in Get-ChildItem -LiteralPath $root -Recurse -File -Force) {
    if ($file.FullName -match '[\\/]node_modules[\\/]|[\\/]vendor[\\/]|[\\/]\.git[\\/]') { continue }
    if ($file.Name -match '(?i)\.(bak(\..*)?|old|orig|tmp|swp)$') { $backupFiles += $file.FullName; continue }
    # .env is expected in a local working copy after tools/Initialize-DT16.ps1; do not print it.
    # ZIP packaging must exclude .env files, and .gitignore prevents accidental commits.
    if ($file.Name -like '.env.*' -and $file.Name -notin @('.env.example','.env.testing','.env.production.example')) { $envLeaks += $file.FullName }
    if ($file.Extension.ToLowerInvariant() -in $extensions -or $file.Name -in @('.env.example','.env.testing','.env.production.example')) {
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) { $bomFiles += $file.FullName }
    }
}
if ($bomFiles.Count) { $bomFiles | ForEach-Object { Write-Host "[BOM] $_" -ForegroundColor Red }; throw "Found $($bomFiles.Count) text files with UTF-8 BOM." }
if ($backupFiles.Count) { $backupFiles | ForEach-Object { Write-Host "[BACKUP] $_" -ForegroundColor Red }; throw "Found $($backupFiles.Count) backup/temp files." }
if ($envLeaks.Count) { $envLeaks | ForEach-Object { Write-Host "[ENV] $_" -ForegroundColor Red }; throw 'Found non-example .env file in source package.' }
Write-Host '[PASS] No BOM or backup/temp files; no non-example .env.* configuration files.' -ForegroundColor Green
Write-Host '[INFO] Local .env files may exist after initialization; verify they are not tracked/packaged.' -ForegroundColor Yellow

$mediaRoot = Join-Path $root 'backend\storage\app\public'
$media = if (Test-Path $mediaRoot) { Get-ChildItem $mediaRoot -Recurse -File } else { @() }
if (($media | Where-Object Extension -in '.jpg','.jpeg','.png','.wav','.mp3').Count -lt 30) { throw 'Demo media package appears incomplete.' }
if (-not (Test-Path (Join-Path $root 'docs\wireframes\01-home.svg'))) { throw 'Wireframe missing.' }
Write-Host "[PASS] Demo media files: $($media.Count)" -ForegroundColor Green
Write-Host '[PASS] Required artifact checks completed. This does not replace running PHPUnit/Playwright.' -ForegroundColor Green
