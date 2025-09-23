<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Console\Events\CommandStarting;

Event::listen(CommandStarting::class, function ($event) {});
