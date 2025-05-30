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
    try {
        $filePath = base_path('storage/app/firebase/sis-task-app-3156d-firebase-adminsdk-fbsvc-6e842eb616.json');

        if (!file_exists($filePath)) {
    throw new \Exception("File Firebase service account tidak ditemukan di: $filePath");
}


        $json = Storage::get($filePath);
        $serviceAccount = json_decode(file_get_contents($filePath), true);

        if (!$serviceAccount || !isset($serviceAccount['client_email'], $serviceAccount['private_key'], $serviceAccount['project_id'])) {
            throw new \Exception("Isi file service account tidak valid atau field penting tidak ditemukan.");
        }

        $this->clientEmail = $serviceAccount['client_email'];
        $this->privateKey = $serviceAccount['private_key'];
        $this->projectId = $serviceAccount['project_id'];

    } catch (\Throwable $e) {
        Log::error("FcmNotificationService Construct Error: " . $e->getMessage());
        // Bisa dilempar exception atau fallback silent
        throw $e; // atau return;
    }
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

    // protected function getAccessToken()
    // {
    //     $serviceAccountPath = storage_path('app/firebase/sis-task-app-3156d-firebase-adminsdk-fbsvc-6e842eb616.json');

    //     $client = new Google_Client();
    //     $client->setAuthConfig($serviceAccountPath);
    //     $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    //     $token = $client->fetchAccessTokenWithAssertion();
    //     return $token['access_token'];
    // }

    protected function getAccessToken()
    {
        try {
            $serviceAccountPath = base_path('storage/app/firebase/sis-task-app-3156d-firebase-adminsdk-fbsvc-6e842eb616.json');

            $client = new Google_Client();
            $client->setAuthConfig($serviceAccountPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $token = $client->fetchAccessTokenWithAssertion();

            if (isset($token['error'])) {
                Log::error("Token error: " . json_encode($token));
                throw new \Exception("Gagal mendapatkan access token: " . $token['error_description']);
            }

            return $token['access_token'];
        } catch (\Throwable $e) {
            Log::error("Firebase Token Error: " . $e->getMessage());
            return null;
        }
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
