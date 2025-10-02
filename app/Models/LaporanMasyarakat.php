<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LaporanMasyarakat extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'laporan_masyarakat';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'form_id',
        'data',
        'status_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function status()
    {
       return $this->belongsTo(StatusLaporan::class, 'status_id', '_id');
    }
}
