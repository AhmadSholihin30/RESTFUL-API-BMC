<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PasienService;
use App\Services\BidanService;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $pasienService;
    protected $bidanService;

    public function __construct(PasienService $pasienService, BidanService $bidanService)
    {
        $this->pasienService = $pasienService;
        $this->bidanService = $bidanService;
    }

    /**
     * Login untuk pasien atau bidan.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');
        $user = null;
        $userType = null;

        // 🔹 Coba login pasien
        $pasien = $this->pasienService->login($credentials);
        if ($pasien) {
            $user = $pasien;
            $userType = 'pasien';
        }

        // 🔹 Coba login bidan
        if (!$user) {
            $bidan = $this->bidanService->login($credentials);
            if ($bidan) {
                $user = $bidan;
                $userType = 'bidan';
            }
        }

        // 🔹 Kalau user tetap null => gagal login
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        // 🔹 Tentukan klaim JWT sesuai tipe user
        $customClaims = $userType === 'pasien'
            ? [
                'no_reg' => (string) $user->no_reg,
                'username' => $user->username,
                'nama' => $user->nama
            ]
            : [
                'id' => (string) $user->id,
                'username' => $user->username,
                'nama' => $user->nama
            ];

        // 🔹 Buat token JWT sesuai guard
        $token = auth($userType)->claims($customClaims)->fromUser($user);

        // 🔹 Simpan token di cookie (1 hari)
        $cookie = cookie('token', $token, 60 * 24);

        // 🔹 Bentuk respons JSON
        $responseData = [
            'message' => 'Login berhasil',
            $userType => $userType === 'pasien'
                ? [
                    'no_reg' => $user->no_reg,
                    'username' => $user->username,
                    'nama' => $user->nama
                ]
                : [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nama' => $user->nama
                ]
        ];

        return response()->json($responseData)->withCookie($cookie);
    }

    /**
     * Logout user - hapus cookie JWT.
     */
    public function logout()
    {
        $cookie = Cookie::forget('token');
        return response()->json(['message' => 'Logout berhasil'])->withCookie($cookie);
    }

    /**
     * Mendapatkan profil user berdasarkan token.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->auth_user,
            'user_type' => $request->auth_type
        ]);
    }
}
