<?php

use App\Http\Controllers\AppController as AppController;
use App\Http\Controllers\CronController;

Route::get('/cron/1/notify-about-ticket', [CronController::class, 'cron1NotifyAboutTicket']);

Route::get('{all}', [AppController::class, 'index'])->where('all', '^((?!api).)*')->name('index');
