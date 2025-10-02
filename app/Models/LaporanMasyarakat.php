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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($laporan) {
            \App\Models\LaporanStatusHistory::where('laporan_id', $laporan->_id)->delete();
        });
    }

    protected $casts = [
        'data' => 'array',
    ];

    public function status()
    {
       return $this->belongsTo(StatusLaporan::class, 'status_id', '_id');
    }

    public function isDitolak(): bool
    {
        return optional($this->status)->label === 'Ditolak';
    }

    public function isPending(): bool
    {
        return optional($this->status)->label === 'Pending';
    }

    public function isSelesai(): bool
    {
        return optional($this->status)->label === 'Selesai';
    }

    public function isDitindaklanjuti(): bool
    {
        return optional($this->status)->label === 'Ditindaklanjuti';
    }
    
    public function history()
    {
        return $this->hasMany(LaporanStatusHistory::class, 'laporan_id', '_id')->orderBy('changed_at', 'desc');
    }

}
