<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\registration;

class Registrationcontroller extends Controller
{
    public function index()
    {
        return view('form');
    }
}
