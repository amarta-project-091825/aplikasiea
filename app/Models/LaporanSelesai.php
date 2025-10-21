<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LaporanSelesai extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'laporan_selesai';
    protected $guarded = [];

     protected $fillable = [
        'form_id',
        'data',
        'tracking_code',
        'status_history',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status_history' => 'array',
    ];
}
