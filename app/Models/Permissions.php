<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    use HasFactory;
    protected $table="permission";
    protected $fillable=[
        'id',
        'name',
        'role',
        'permissions',
        'read',
        'write',
    ];   
    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo('App\Models\Roles','id');
    }
}
