<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LaporanStatusHistory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'laporan_status_history';

    protected $fillable = [
        'laporan_id',
        'status_id',
        'status_label',
        'changed_at',
        'changed_by',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanMasyarakat::class, 'laporan_id', '_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusLaporan::class, 'status_id', '_id');
    }
}
