<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the current user
        $user = Auth::user();

        // Calculate the sum of views for videos uploaded by the current user
        $totalViews = Video::where('userid', $user->id)->sum('views');

        // Count the total videos uploaded by the current user
        $totalVideos = Video::where('userid', $user->id)->count();

        // Pass the user, total views, and total videos to the view
        return view('dashboard', compact('user', 'totalViews', 'totalVideos'));
    }
}
