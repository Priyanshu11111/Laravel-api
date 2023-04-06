<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    use HasFactory;
    protected $table="suppliercontact";
    protected $fillable=[
        'id',
        'supplier_name',
        'address',
        'number',
        'supplier_id',
    ];
}
