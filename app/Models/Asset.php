<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    protected $table='asset';
    protected $fillable=[
        'id',
        'name',
        'model',
        'ownername',
        'status',
    ];
    public function model(){
        return $this->belongsTo(Models::class,'model');
    }
    public function ownername(){
        return $this->belongsTo(Customer::class,'ownername');
    }
}



