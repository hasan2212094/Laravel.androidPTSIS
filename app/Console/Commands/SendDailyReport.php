<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'mengirim laporan harian ';

    /**
     * Execute the console command.
     */
    protected $signature = 'notification:send';
    protected $description = 'Send notification via API';


     public function handle()
    {
        // Contoh pemanggilan API POST
        $response = Http::post('https://https://sribaruindahsejahtera.net/api/send-notification', [
            'message' => 'Notifikasi ingat pesan tugas anda',
            'title' => 'success',
        ]);

        if ($response->successful()) {
            $this->info('Notifikasi berhasil dikirim.');
        } else {
            $this->error('Gagal mengirim notifikasi: ' . $response->body());
        }
    }
}
