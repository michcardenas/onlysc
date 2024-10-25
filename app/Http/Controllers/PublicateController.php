<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicateController extends Controller
{
    public function showRegistrationForm()
    {
        return view('publicate');
    }
}
