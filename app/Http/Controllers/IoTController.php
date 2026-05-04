<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class IoTController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // --- DI IoTController.php ---
    public function store(Request $request)
    {
        $data = $request->validate([
            'soil' => 'required',
            'water' => 'required',
            'pir' => 'required', // Pastikan divalidasi sebagai 'pir'
        ]);

        $data['created_at'] = now()->toDateTimeString();

        // Simpan ke path yang sama dengan dashboard
        $this->database->getReference('iot/latest')->set($data);
        $this->database->getReference('iot/logs')->push($data);

        return response()->json(['status' => 'success']);
    }
}
