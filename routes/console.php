<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:reset-daily')->dailyAt('00:01');
