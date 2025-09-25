<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class FormFieldType extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'form_field_types';
    protected $primaryKey = '_id';
    public $incrementing = true;

    protected $fillable = ['name', 'value', 'description'];
}
