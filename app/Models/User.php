<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; // ← ini dari mongodb/laravel-mongodb
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [ 'name', 'email', 'password', 'role_id', ];

    protected $hidden = [ 'password', 'remember_token', ];

     protected $casts = [ 'role_id' => 'int', ];

     public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', '_id');
    }

      /**
     * Cek apakah user punya role tertentu.
     *
     * @param  int|string  $idOrSlug  Bisa id integer (1,2,3) atau slug string (admin, petugas-data, ...)
     * @return bool
     */

    public function hasRole($idOrSlug): bool
    {
        if (is_null($this->role_id)) return false;
        if (is_numeric($idOrSlug)) return (int)$this->role_id === (int)$idOrSlug;
        return optional($this->role)->slug === $idOrSlug;
    }


}
