# ============================================================
# TaskFlow – Đăng ký Windows Task Scheduler
# Chạy file này với quyền Administrator (1 lần duy nhất)
# ============================================================

$phpExe  = "C:\Users\admin\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
$workDir = "D:\New folder\taskflow"
$logFile = "D:\New folder\taskflow\storage\logs\scheduler.log"

# Xóa task cũ nếu tồn tại
Unregister-ScheduledTask -TaskName "TaskFlow-Scheduler" -Confirm:$false -ErrorAction SilentlyContinue

$action = New-ScheduledTaskAction `
    -Execute $phpExe `
    -Argument "`"$workDir\artisan`" schedule:run" `
    -WorkingDirectory $workDir

# Lặp mỗi 1 phút trong 10 năm
$trigger = New-ScheduledTaskTrigger `
    -Once `
    -At (Get-Date) `
    -RepetitionInterval (New-TimeSpan -Minutes 1) `
    -RepetitionDuration (New-TimeSpan -Days 3650)

$settings = New-ScheduledTaskSettingsSet `
    -ExecutionTimeLimit (New-TimeSpan -Minutes 1) `
    -MultipleInstances IgnoreNew `
    -StartWhenAvailable

Register-ScheduledTask `
    -TaskName    "TaskFlow-Scheduler" `
    -Action      $action `
    -Trigger     $trigger `
    -Settings    $settings `
    -RunLevel    Highest `
    -Description "TaskFlow: Kiem tra deadline moi phut va ban Windows Toast Notification" `
    -Force

Write-Host ""
Write-Host "✅ Đã đăng ký TaskFlow-Scheduler thành công!" -ForegroundColor Green
Write-Host "   → Chạy mỗi 1 phút, tự bắn Windows Toast khi task sắp đến hạn"
Write-Host "   → Log: $logFile"
Write-Host ""

# Chạy ngay lập tức 1 lần để test
Write-Host "▶ Chạy thử ngay bây giờ..." -ForegroundColor Cyan
Start-ScheduledTask -TaskName "TaskFlow-Scheduler"
Start-Sleep -Seconds 3
Get-ScheduledTaskInfo -TaskName "TaskFlow-Scheduler" | Select-Object LastRunTime, LastTaskResult, NextRunTime
