<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\Sensor;

class SensorController extends Controller
{
    public function getData()
    {
        $firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();

        $data = $firebase->getReference('sensor')->getValue();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();

        $data = $request->all();

        // 🔥 SIMPAN KE FIREBASE
        $firebase->getReference('sensor')->set($data);

        // 🔥 SIMPAN KE MYSQL
        Sensor::create([
            'pir' => $request->input('pir'),
            'soil' => $request->input('soil'),
            'water' => $request->input('water'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // 🔥 TAMBAHAN DI SINI
    public function history()
    {
        return response()->json(Sensor::latest()->get());
    }
}