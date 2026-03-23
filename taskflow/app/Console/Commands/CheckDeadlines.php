<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Setting;
use Carbon\Carbon;

class CheckDeadlines extends Command
{
    protected $signature   = 'tasks:check-deadlines {--force : Bắn toast thử cho task đầu tiên, bỏ qua cửa sổ thời gian} {--window=3 : Cửa sổ kiểm tra tính bằng phút (mặc định 3)}';
    protected $description = 'Kiểm tra deadline sắp đến và bắn Windows Toast Notification';

    // File lưu lịch sử đã nhắc → tránh nhắc trùng
    private string $stateFile;

    public function __construct()
    {
        parent::__construct();
        $this->stateFile = storage_path('app/notif_state.json');
    }

    public function handle(): void
    {
        $notifyBefore = Setting::get('notifyBefore', [1440]);
        $now          = Carbon::now();
        $state        = $this->loadState();
        $changed      = false;
        $window       = (int) $this->option('window');

        // --force: bắn thử 1 toast ngay, không cần đúng cửa sổ thời gian
        if ($this->option('force')) {
            $task = Task::whereNotNull('deadline')->first();
            if ($task) {
                $this->fireToast($task, 1440, 1440);
                $this->info('Force toast fired for: ' . $task->title);
            } else {
                $this->warn('Không có task nào có deadline.');
            }
            return;
        }

        $tasks = Task::where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->get();

        foreach ($tasks as $task) {
            $deadline = Carbon::parse($task->deadline)->endOfDay();
            $diffMin  = $now->diffInMinutes($deadline, false); // âm = quá hạn

            if ($diffMin < 0) continue;

            foreach ($notifyBefore as $minutes) {
                // Cửa sổ N phút để không bắn trùng giữa 2 lần chạy
                if ($diffMin <= $minutes && $diffMin > $minutes - $window) {
                    $stateKey = "task_{$task->id}_min_{$minutes}";

                    // Đã nhắc cho deadline này chưa?
                    $deadlineStr = $task->deadline->format('Y-m-d');
                    if (($state[$stateKey] ?? '') === $deadlineStr) {
                        continue;
                    }

                    $this->fireToast($task, $diffMin, $minutes);
                    $state[$stateKey] = $deadlineStr;
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $this->saveState($state);
        }

        $this->info('[' . $now->format('H:i:s') . '] Checked ' . $tasks->count() . ' tasks.');
    }

    // ─── Windows Toast Notification ───────────────────────────────────────────

    private function fireToast(Task $task, float $diffMin, int $minutesBefore): void
    {
        if ($diffMin < 60) {
            $timeText = round($diffMin) . ' phút';
        } elseif ($diffMin < 1440) {
            $timeText = round($diffMin / 60) . ' giờ';
        } else {
            $timeText = round($diffMin / 1440) . ' ngày';
        }

        $icon = match($task->priority) {
            'high'   => '🔴',
            'medium' => '🟡',
            default  => '🟢',
        };

        $deadlineStr = $task->deadline->format('d/m/Y');
        $title   = $this->escapePs("{$icon} Nhắc việc: {$task->title}");
        $body    = $this->escapePs("⏰ Còn {$timeText}  ·  📅 {$deadlineStr}  ·  📁 {$task->category}");
        $appName = 'TaskFlow';

        // PowerShell script bắn Windows Toast
        $ps = <<<PS
Add-Type -AssemblyName System.Runtime.WindowsRuntime
[void][Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType=WindowsRuntime]
[void][Windows.Data.Xml.Dom.XmlDocument, Windows.Data.Xml.Dom.XmlDocument, ContentType=WindowsRuntime]

\$xml = @"
<toast activationType="protocol" launch="http://localhost:8000">
  <visual>
    <binding template="ToastGeneric">
      <text>{$title}</text>
      <text>{$body}</text>
    </binding>
  </visual>
  <actions>
    <action content="Mở TaskFlow" activationType="protocol" arguments="http://localhost:8000"/>
  </actions>
</toast>
"@

\$doc = [Windows.Data.Xml.Dom.XmlDocument]::new()
\$doc.LoadXml(\$xml)
\$toast = [Windows.UI.Notifications.ToastNotification]::new(\$doc)
[Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier('{$appName}').Show(\$toast)
PS;

        // Ghi ra file tạm với UTF-8 BOM để PowerShell đọc đúng tiếng Việt
        $tmpFile = storage_path('app/toast_tmp.ps1');
        file_put_contents($tmpFile, "\xEF\xBB\xBF" . $ps);

        $cmd = 'powershell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File "' . $tmpFile . '"';
        shell_exec($cmd);

        $this->line("  → Toast fired: [{$task->priority}] {$task->title} (còn {$timeText})");
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function escapePs(string $str): string
    {
        // Escape ký tự đặc biệt trong XML
        return htmlspecialchars($str, ENT_XML1, 'UTF-8');
    }

    private function loadState(): array
    {
        if (!file_exists($this->stateFile)) return [];
        $data = json_decode(file_get_contents($this->stateFile), true);
        return is_array($data) ? $data : [];
    }

    private function saveState(array $state): void
    {
        // Giữ tối đa 500 entries để file không phình
        if (count($state) > 500) {
            $state = array_slice($state, -500, 500, true);
        }
        file_put_contents($this->stateFile, json_encode($state, JSON_PRETTY_PRINT));
    }
}
