<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class SupplierContact extends Model
{
    use HasFactory,HasApiTokens;
    protected $table="suppliercontact";
    protected $fillable=[
        'id',
        'supplier_name',
        'address',
        'number',
        'supplier_id',
    ];
}
