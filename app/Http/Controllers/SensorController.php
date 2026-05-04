<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Library bawaan Laravel

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = [
                'soil' => (int) $request->soil,
                'water' => (float) $request->water,
                'pir' => $request->pir,
                'created_at' => now()->toDateTimeString(),
            ];

            // Masukkan URL Firebase Anda di sini
            $url = 'https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app/';

            // Kirim data ke Firebase (Format REST API)
            // Penting: Tambahkan .json di akhir nama path-nya
            Http::put($url.'iot/latest.json', $data);
            Http::post($url.'iot/logs.json', $data);

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getData()
    {
        $url = 'https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app/iot/latest.json';
        $response = Http::get($url);

        return response()->json($response->json());
    }

    public function toggleServo(Request $request)
    {
        try {
            // 1. Ambil status dari tombol web (1 atau 0)
            $status = $request->status;

            // 2. URL Firebase ke folder control (tambahkan .json di akhir)
            $url = 'https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app/iot/control/pintu_air.json';

            // 3. Update nilai di Firebase
            // Kita kirim angka langsung (tanpa array) karena di ESP32 kita pakai payload.toInt()
            Http::put($url, $status);

            return response()->json(['status' => 'success', 'manual_status' => $status], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
