<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StatusLaporan extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'status_laporan';
    protected $fillable = ['label'];
}

