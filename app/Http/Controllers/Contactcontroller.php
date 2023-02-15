<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Contactcontroller extends Controller
{
    public function upload(Request $request)
    {
       echo $request->file('image')->store('upload');
    }
}
