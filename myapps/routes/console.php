<?php

use Illuminate\Support\Facades\Schedule;

// Schedule laporan 6 bulanan: setiap tanggal 1 bulan Januari dan Juli pukul 09:00
Schedule::command('laporan:send-6bulanan')
    ->cron('0 9 1 1,7 *')
    ->timezone('Asia/Jakarta')
    ->emailOutputOnFailure(config('mail.from.address'));

