<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LaporanStatus extends Model
{
    protected $table = 'laporan_status';

    protected $fillable = ['nama_status'];
}
