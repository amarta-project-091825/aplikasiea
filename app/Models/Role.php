<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    public $timestamps = true;

    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['_id','name','slug','description'];

    protected $casts = ['_id' => 'int'];

    public function attributes()
{
    return $this->hasMany(\App\Models\Admin\FormAttribute::class, 'form_id', '_id');
}

}

