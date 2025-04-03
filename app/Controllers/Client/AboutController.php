<?php

namespace App\Controllers\Client;

use App\Controller;


class AboutController extends Controller
{
    public function index()
    {
        
        $heading1 = 'Trang Giới Thiệu';
        $subHeading1 = '👌👌👌👌👌';

        return view('Client.about', compact('heading1', 'subHeading1'));
    }
}
