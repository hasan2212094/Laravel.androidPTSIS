<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FcmNotificationService;

class NotificationController extends Controller
{
    public function send(FcmNotificationService $fcm)
    {
        $title = "Pengingat Harian";
        $body = "Cek tugas hari ini ya!";
        $data = ["type" => "daily_reminder"];

        $fcm->sendNotification($title, $body, $data);

        return response()->json(['status' => 'Notifikasi dikirim']);
    }
}
