<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    protected $clientEmail;
    protected $privateKey;
    protected $projectId;

    public function __construct()
    {
        $serviceAccount = json_decode(Storage::get('firebase/sis-task-app-3156d-firebase-adminsdk-fbsvc-6e842eb616.json'), true);
        $this->clientEmail = $serviceAccount['client_email'];
        $this->privateKey = $serviceAccount['private_key'];
        $this->projectId = $serviceAccount['project_id'];
    }

    // protected function getAccessToken()
    // {
    //     $client = new Google_Client();
    //     $client->setAuthConfig([
    //         "type" => "service_account",
    //         "client_email" => $this->clientEmail,
    //         "private_key" => $this->privateKey,
    //     ]);
    //     $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    //     $token = $client->fetchAccessTokenWithAssertion();
    //     return $token['access_token'];
    // }

    protected function getAccessToken()
    {
        $serviceAccountPath = storage_path('app/firebase/sis-task-app-3156d-firebase-adminsdk-fbsvc-6e842eb616.json');

        $client = new Google_Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }


    public function sendNotification($title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        //Log::info('title: ' . $title . ', body: ' . $body . ', data: ' . $data);
        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                "message" => [
                    //"token" => "your_target_device_token", // Atau gunakan "topic" => "all"
                    "topic" => "all",
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => $data,
                ]
            ]);

        return $response->json();
    }
}
