<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PesanService;

class PesanController extends Controller
{
    protected $service;

    public function __construct(PesanService $service)
    {
        $this->service = $service;
    }

    // Kirim pesan: POST /pesan/{pengirimId}/{penerimaId}
    public function kirimPesan(Request $request, $pengirimId, $penerimaId)
    {
        $request->validate([
            'isi_pesan' => 'required|string',
        ]);

        $pesan = $this->service->kirimPesan(
            $request->isi_pesan,
            $pengirimId,
            $penerimaId
        );

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'data' => $pesan
        ], 201);
    }

    // Ambil pesan: GET /pesan/{bidanId}/{pasienId}
    public function ambilPesan($bidanId, $pasienId)
    {
        $pesans = $this->service->ambilPesan($bidanId, $pasienId);

        return response()->json($pesans);
    }
}
