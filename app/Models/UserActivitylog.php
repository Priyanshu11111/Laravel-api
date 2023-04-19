<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class UserActivitylog extends Authenticatable 
{
    use HasFactory,HasApiTokens;
    protected $table="user_activitylogs";
    protected $fillable=[
        'id',
        'email',
        'modifyuser',
        'data_time',
    ];   

}
