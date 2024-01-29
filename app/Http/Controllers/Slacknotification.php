<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class Slacknotification extends Controller
{
    public function SlackNotification($message){
        $noti = new \App\Notifications\Slacknotification($message);
        Notification::route('slack', env('LOG_SLACK_WEBHOOK_URL'))->notify($noti);
    }
}


