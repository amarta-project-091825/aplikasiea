<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Form extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'forms';
    protected $primaryKey = '_id';
    public $incrementing = true;

    protected $fillable = [
        'name', 'slug', 'description', 'fields', 'is_active'
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
    ];

    // Contoh struktur fields (disimpan sebagai array):
    // [
    //   ['label' => 'Nama', 'name' => 'nama', 'type' => 'text', 'required' => true, 'placeholder'=>'', 'options'=>[], 'min'=>null, 'max'=>null],
    //   ['label' => 'Kategori', 'name' => 'kategori', 'type' => 'select', 'required' => true, 'options'=>['A','B','C']]
    // ]
}
