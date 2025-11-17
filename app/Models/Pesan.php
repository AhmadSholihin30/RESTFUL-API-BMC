<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'isi_pesan',
        'waktu_kirim',
        'pengirim_id',
        'penerima_id',
    ];

    // Relasi pengirim (bidan atau pasien)
    public function pengirim()
    {
        return \App\Models\Bidan::find($this->pengirim_id) ?? \App\Models\Pasien::find($this->pengirim_id);
    }

    // Relasi penerima (bidan atau pasien)
    public function penerima()
    {
        return \App\Models\Bidan::find($this->penerima_id) ?? \App\Models\Pasien::find($this->penerima_id);
    }
}
