<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    public function index(){
        $modules=Modules::get();
        return response()->json($modules);
    }
}
