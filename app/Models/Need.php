<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Foundation\Auth\User as Authenticatable;

class Need extends Model
{
    use HasFactory;
    protected $table="request";
    protected $fillable=[
        'id',
        'types',
        'model',
        'requestreason',
    ];
    public function user()
    {
        return $this->belongsTo(Customer::class);
    }
    public function type()
    {
        return $this->belongsTo(Types::class,'types');
    }
    public function model()
    {
        return $this->belongsTo(Models::class,'models');
    }
}
