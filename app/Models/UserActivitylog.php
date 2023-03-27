<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivitylog extends Model
{
    use HasFactory;
    protected $table="user_activitylogs";
    protected $fillable=[
        'id',
        'email',
        'modifyuser',
        'data_time',
    ];   

}
