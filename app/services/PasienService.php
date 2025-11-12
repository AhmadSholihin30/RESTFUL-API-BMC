<?php

namespace App\Services;

use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;

class PasienService
{
    public function login(array $credentials)
    {
        $pasien = Pasien::where('username', $credentials['username'])->first();

        if ($pasien && Hash::check($credentials['password'], $pasien->password)) {
            return $pasien;
        }

        return null;
    }

    public function updateProfile(Pasien $pasien, array $data)
    {
        $pasien->update($data);
        return $pasien;
    }
}
