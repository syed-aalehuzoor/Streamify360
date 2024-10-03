<?php

namespace App\Http\Controllers;

use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Server;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use App\Models\UserSetting;

class VideoController extends Controller
{

    public function index(Request $request)
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Retrieve the search query from the request
        $query = $request->input('query', '');

        // Retrieve videos that belong to the authenticated user and match the search query
        $videos = Video::where('userid', $userId)
                        ->where('name', 'like', '%'.$query.'%')
                        ->whereIn('status', ['Initiated', 'Processing', 'Live', 'Failed'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Pass the videos and query to the view
        return view('videos', [
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    public function drafts(Request $request)
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Retrieve the search query from the request
        $query = $request->input('query', '');

        // Retrieve videos that belong to the authenticated user and match the search query
        $videos = Video::where('userid', $userId)
                        ->where('name', 'like', '%'.$query.'%')
                        ->whereIn('status', ['Draft', 'URL'])
                        ->paginate(10);

        // Pass the videos and query to the view
        return view('drafts', [
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    public function create()
    {
        return view('add-video');
    }

    public function player($id)
    {
        // Fetch the video from the database
        $video = Video::findOrFail($id);

        // Check if the video is not found or if it has been deleted
        if ($video && $video->status === 'live') {
            $video->increment('views');
            $settings = UserSetting::where('user_id', $video->userid)->first();
            if (!$settings) {
                $settings = UserSetting::create(['user_id' => $video->userid]);
            }           
            $referer = request()->headers->get('referer'); 
            $allowedDomains = json_decode($settings->allowed_domains);
            $currentDomain = request()->getHost();
            $allowedDomains[] = $currentDomain;
            if ($referer) {
                // Parse the referer URL to get the domain
                $refererDomain = parse_url($referer, PHP_URL_HOST);
                //dd($refererDomain, $allowedDomains);
                if (!in_array($refererDomain, $allowedDomains)) {
                    abort(403, 'Unauthorized Video Source');
                }
            } else {
                abort(403, 'No referer found');
            }
            return view('player', compact('video', 'settings'));
        }
        abort(404);
    }

    public function edit($id)
    {
        // Fetch the video from the database
        $video = Video::findOrFail($id);
        
        // Return the view with the video data
        return view('edit-video', compact('video'));
    }

    public function destroy($id)
    {
        // Find the video by ID
        $video = Video::find($id);

        // Delete the video
        $video->update(['status' => 'Deleted']);

        // Redirect or return a response
        return redirect()->route('all-videos')->with('success', 'Video Deleted successfully.');
        
    }

}
