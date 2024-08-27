<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function general()
    {
        return view('general-settings');
    }

    public function video()
    {
        return view('videos-settings');
    }
}
