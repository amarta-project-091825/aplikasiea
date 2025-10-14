<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public static function send($target, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62',
        ]);

        return $response->json(); // <-- pastikan ini return array API Fonnte
    }
}
