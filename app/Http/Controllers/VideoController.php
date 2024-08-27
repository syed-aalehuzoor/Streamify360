<?php

namespace App\Http\Controllers;

use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Server;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;


class VideoController extends Controller
{
    private function getServerId()
    {
        $servers = Server::all();
        // Implement your logic to retrieve the server ID
        return 11; // Example static value, replace with actual logic
    }

    public function index(Request $request)
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Retrieve the search query from the request
        $query = $request->input('query', '');

        // Retrieve videos that belong to the authenticated user and match the search query
        $videos = Video::where('userid', $userId)
                        ->where('name', 'like', '%'.$query.'%')
                        ->paginate(10);

        // Pass the videos and query to the view
        return view('videos', [
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
        
        // Return the view with the video data
        return view('player', compact('video'));
    }

    public function edit($id)
    {
        // Fetch the video from the database
        $video = Video::findOrFail($id);
        
        // Return the view with the video data
        return view('edit-video', compact('video'));
    }

    public function save(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'videoname' => 'nullable|string|max:2000',
            'thumbnail' => 'nullable|file|image|max:2000',
        ]);

        // Retrieve the video by ID
        $video = Video::findOrFail($id);

        // Update fields
        $video->name = $request->input('videoname', $video->name);

        // Handle file uploads
        if ($request->hasFile('thumbnail')) {
            $video->thumbnail_url = Storage::url($request->file('thumbnail')->store('thumbnails'));
        }

        // Save changes
        $video->save();

        return redirect()->route('videos.edit', $video->id)->with('success', 'Video updated successfully.');
    }

    public function destroy($id)
    {
        // Find the video by ID
        $video = Video::find($id);

        // Delete the video
        $video->delete();

        // Redirect or return a response
        return redirect()->route('all-videos')->with('success', 'Video Deleted successfully.');
        
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'videoname' => 'nullable|string|max:2000',
            'video' => 'required|file|mimes:mp4,avi,mkv|max:10000',
            'subtitle' => 'nullable|file|mimes:srt,vtt|max:5000',
            'logo' => 'nullable|file|image|max:2000',
            'thumbnail' => 'nullable|file|image|max:2000',
        ]);

        $videoPath = $request->file('video')->store('videos');
        // Handle file uploads and store paths
        $videoname = $request->input('videoname') ?? pathinfo($request->file('video')->getClientOriginalName(), PATHINFO_FILENAME);
        $subtitlePath = $request->file('subtitle') ? $request->file('subtitle')->store('subtitles') : null;
        $logoPath = $request->file('logo') ? $request->file('logo')->store('logos') : null;
        $thumbnailPath = $request->file('thumbnail') ? $request->file('thumbnail')->store('thumbnails') : null;
        
        // Create a new video record
        $video = Video::create([
            'name' => $videoname,
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(), // Implement your logic to get the server ID
            'status'=>'Initiated',
            'thumbnail_url' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
            'subtitle_filepath' => $subtitlePath,
            'subtitle_url' => $subtitlePath ? Storage::url($subtitlePath) : null,
            'logo_filepath' => $logoPath,
            'video_filepath' => $videoPath, // Add this line
        ]);

    
        // Prepare and run the process to execute the Python script
        $startkey = env('START_KEY');
        $pythonkey = env('PYTHON_KEY');
        $scriptPath = '../scripts/process_video.py';
    
        $process = new Process([$startkey, $pythonkey, $scriptPath, '--id', $video->id],
            null,
            ['SYSTEMROOT' => getenv('SYSTEMROOT'), 'PATH' => getenv("PATH")]);
    
        $process->run();

        // Redirect or return a response
        return redirect()->route('all-videos')->with('success', 'Video uploaded successfully.');
    }    
}
