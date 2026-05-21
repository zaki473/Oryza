<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = [
                'soil'       => (int) $request->soil,
                'water'      => (float) $request->water,
                'pir'        => $request->pir,
                'mode'       => $request->mode, // Pastikan menangkap variabel mode
                'created_at' => now()->toDateTimeString(),
            ];

            $url = 'https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app/';

            // Kirim ke Firebase
            Http::put($url . 'iot/latest.json', $data);
            Http::post($url . 'iot/logs.json', $data);

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
            $status = $request->status;
            $url = 'https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app/iot/control/pintu_air.json';
            Http::put($url, $status);
            return response()->json(['status' => 'success', 'manual_status' => $status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
