<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Users;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if(Auth::user()->usertype=='admin')
        {
            return redirect('/admin');
        }
        else if(Auth::user()->usertype=='user'){
            return redirect('/dashboard');
        }
        abort(404);
        
    }
}
