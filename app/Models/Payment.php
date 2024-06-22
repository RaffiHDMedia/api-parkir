<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'notrans', 'type', 'plat', 'biaya', 'masuk', 'jenis', 'checkout_link', 'external_id', 'status'
    ];
}
