<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class UserModel extends Model
{
    use HasApiTokens;
    public $table="users";
    public $timestamps=false;
    protected $fillable = [
        'name', 'email', 'password',
    ];
}
