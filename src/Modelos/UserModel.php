<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birthday',
        'password'
    ];
    protected $hidden = [
        'password'
    ];
}
