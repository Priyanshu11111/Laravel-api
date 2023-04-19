<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
class Models extends  Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $table="models";
    protected $fillable=[
        'id',
        'types',
        'supplier',
        'name',
        'alias'
    ];
    //this for Modelscontroller
       public function type()
    {
        return $this->belongsTo(Types::class,'types');
    }
    // This for Typescontroller
    public function types()
    {
        return $this->belongsTo('App\Models\Types');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier');
    }
    public function needs()
    {
        return $this->belongsTo('App\Models\Need','');
    }
}
