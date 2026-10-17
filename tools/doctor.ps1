$checks=@(@{n='git';c='git --version'},@{n='php';c='php -v'},@{n='composer';c='composer --version'},@{n='mysql';c='mysql --version'},@{n='node';c='node -v'},@{n='python';c='python --version'},@{n='docker';c='docker --version'})
Write-Host '== CSE703073 Environment Check =='
foreach($k in $checks){$exe=$k.c.Split(' ')[0];if(Get-Command $exe -ErrorAction SilentlyContinue){Write-Host "[OK] $($k.n): $((Invoke-Expression $k.c 2>&1|Select-Object -First 1))"}else{Write-Host "[THIEU] $($k.n)" -ForegroundColor Yellow}}
'pdo_mysql','mbstring','openssl','fileinfo','curl','zip','intl','gd' | % { if((php -m)-contains $_){Write-Host "[OK] PHP $_"} else {Write-Host "[THIEU] PHP $_" -ForegroundColor Yellow} }
