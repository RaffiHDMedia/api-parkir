<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parkir extends Model
{
    use HasFactory;

    protected $table = 'parkir';

    protected $fillable = [
        'notrans', 'plat', 'type', 'tanggal', 'camM', 'camM2', 'camK', 'camK2', 'masuk', 'keluar', 'lama', 'pertama', 'perjam', 'denda', 'total', 'status', 'seri', 'keterangan', 'ken', 'user','shift', 'userK', 'shiftK', 'pMasuk', 'pKeluar'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'masuk' => 'datetime',
        'keluar' => 'datetime',
    ];

    public $timestamps = false;
}
