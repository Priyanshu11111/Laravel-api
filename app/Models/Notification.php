<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Notification extends Authenticatable
{
    use HasFactory;
    protected $table="notifications";
    protected $fillable=[
        'id',
        'data',
        'notifiable_id',
    ];

}
