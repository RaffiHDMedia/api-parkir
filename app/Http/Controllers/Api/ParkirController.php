<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parkir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ParkirController extends Controller
{
    // Endpoint untuk mengambil data parkir berdasarkan notrans dari query parameter
    public function index(Request $request)
    {
        // Ambil notrans dari query parameter
        $notrans = $request->query('notrans');

        Log::info('Notrans from query parameter:', ['notrans' => $notrans]);

        if (!$notrans) {
            return response()->json(['message' => 'No parkir entry found in query parameter'], 404);
        }

        // Cari data parkir berdasarkan notrans
        $parkir = Parkir::where('notrans', $notrans)->first();
        if (!$parkir) {
            return response()->json(['message' => 'Parkir entry not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data ditampilkan',
            'data' => $parkir,
        ], 200);
    }

    // Endpoint untuk menyimpan data parkir baru
    public function store(Request $request)
    {
        $request->validate([
            'plat' => 'required|string|max:255',
            'type' => 'required|string|in:S,M',
        ]);

        // Generate notrans
        $notrans = str_pad(random_int(0, 999999999), 11, '0', STR_PAD_LEFT);
        $tanggal = Carbon::now()->toDateString();
        $masuk = Carbon::now();
        $pertama = $request->type == 'S' ? 2000 : 5000;
        $total = $pertama;

        // Simpan data parkir baru
        $parkir = Parkir::create([
            'notrans' => $notrans,
            'plat' => $request->plat,
            'type' => $request->type,
            'tanggal' => $tanggal,
            'masuk' => $masuk,
            'pertama' => $pertama,
            'total' => $total,
            'status' => 'P',
            'ken' => $request->type == 'S' ? 'MOTOR' : 'MOBIL',
            'user' => 'admin',
            'shift' => 0,
            'userK' => 'admin',
            'shiftK' => 0,
            'pMasuk' => 2,
        ]);

        Log::info('Notrans stored:', ['notrans' => $parkir->notrans]);

        if ($parkir->notrans) {
            return response()->json([
                'status' => true,
                'message' => 'Sukses Memasukkan Data',
                'notrans' => $parkir->notrans, // Mengembalikan notrans setelah berhasil disimpan
            ], 200);
        } else {
            return response()->json(['message' => 'Failed to save parkir data'], 500);
        }
    }

    
}
