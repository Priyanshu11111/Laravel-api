<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Authenticatable
{
    use Notifiable,HasFactory,HasApiTokens;
    protected $table="customers";
    protected $fillable=[
        'id',
        'firstname',
        'lastname',
        'email',
        'password',
        'password_confirmation',
    ];   
}

