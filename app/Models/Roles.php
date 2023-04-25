<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $table="roles";
    protected $fillable=[
      'id',
      'name'
    ];
    public function user()
    {
        return $this->belongsTo(Customer::class);
    }
    public function permissions(){
      return $this->hasMany('App\Models\Permissions','role');
    }
}
