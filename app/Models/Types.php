<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Models;
use App\Models\Need;

class Types extends Model
{
    use HasFactory,notifiable;
    protected $table="types";
    protected $fillable=[
        'id',
        'name',
        'comment',
    ];   
    public function model(){
        return $this->hasMany(Models::class);
    }
    //
    public function models()
    {
        return $this->hasMany('App\Models\Models','types');
    }

    public function needs()
    {
        return $this->hasMany('App\Models\Need','types');
    }
}
