<?php

namespace App\Controllers\Admin;

use App\Controller;

class DashboarController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}