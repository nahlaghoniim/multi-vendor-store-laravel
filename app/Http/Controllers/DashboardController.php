<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //Actions 
    public function index ()
    {$user = 'Nahla Ghonim';
return view('dashboard.index', compact('user'));

    }
}
