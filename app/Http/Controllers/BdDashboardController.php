<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BdDashboardController extends Controller
{
    public function index()
    {
        return view('bd.dashboard');
    }
}
