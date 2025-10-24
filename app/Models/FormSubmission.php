<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class FormSubmission extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'form_submissions';
    protected $primaryKey = '_id';
    public $incrementing = true;

    protected $fillable = [
        'form_id', 'data', 'files', 'submitted_by', 'geometry'
    ];

    protected $casts = [
        'data' => 'array',
        'files' => 'array',
    ];

    public function getDataAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function getFilesAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', '_id');
    }
}
