<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Kiểm tra deadline mỗi phút và bắn Windows Toast khi sắp đến hạn
Schedule::command('tasks:check-deadlines')->everyMinute();
