<?php

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;

Event::listen(CommandStarting::class, function ($event) {});
