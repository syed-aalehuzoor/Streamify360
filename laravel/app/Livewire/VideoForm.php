<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class VideoForm extends Component
{
    use WithFileUploads;

    // Component properties for storing form data
    public $video_id;
    public $video;
    public $videoStatus; // To track video status
    public $currentStep = 1;
    
    public $logo;
    public $subtitle;
    public $thumbnail;
    public $videoname;

    // Declare properties for YouTube, Google Drive, and Direct URLs
    public $youtubeUrl;
    public $driveUrl;
    public $directUrl;
    

    public function checkVideoStatus()
    {
        $video = Video::find($this->video_id);
        if ($video) {
            $this->videoStatus = $video->status; // Update the status
        }
        if ($this->videoStatus === 'Ready') {
            $this->emit('videoStatusChangedToReady');
        }
    }


    public function mount()
    {
        // Check if there is a query parameter for 'video_id'
        if (request()->has('video_id')) {
            $video_id = (string) request()->get('video_id');
            $this->video_id = $video_id;
            $this->currentStep = 2;
        }
    }    
    
    public function submitYouTubeUrl(){
        $this->validate([
            'youtubeUrl' => 'required|url', // Ensure the URL is valid
        ]);
    
        $video = Video::create([
            'name' => 'Untitled Video from YouTube',
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(),
            'status' => 'Draft',
            'video_url' => $this->youtubeUrl, // Store the actual YouTube URL
        ]);
        $this->video_id = $video->id;
        $this->currentStep = 2;
    }

    public function submitDriveUrl(){
        $this->validate([
            'driveUrl' => 'required|url', // Ensure the URL is valid
        ]);
    
        $video = Video::create([
            'name' => 'Untitled Video From Google Drive',
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(),
            'status' => 'Draft',
            'video_url' => $this->driveUrl, // Store the actual Drive URL
        ]);
        $this->video_id = $video->id;
        $this->currentStep = 2;
    }

    public function submitDirectUrl(){
        $this->validate([
            'directUrl' => 'required|url', // Ensure the URL is valid
        ]);
    
        $video = Video::create([
            'name' => 'Untitled Video',
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(),
            'status' => 'Draft',
            'video_url' => $this->directUrl, // Store the actual Direct URL
        ]);
        $this->video_id = $video->id;
        $this->currentStep = 2;
    }

    // Updated video method to trigger the video upload
    public function updatedVideo()
    {
        $this->validate(['video' => 'required|file|mimetypes:video/mp4,video/x-matroska|max:102400']);
        $this->uploadVideo();
    }

    // Uploads the video file and creates a video entry in the database
    public function uploadVideo()
    {
        $videoname = pathinfo($this->video->getClientOriginalName(), PATHINFO_FILENAME);

        // Create a video entry with status "Draft"
        $video = Video::create([
            'name' => $videoname,
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(),
            'status' => 'Draft',
            'video_url' => 'pending', // Placeholder for video URL
        ]);

        $this->video_id = $video->id;

        // Handle file upload and update progress
        $extension = $this->video->getClientOriginalExtension();
        $videoPath = $this->video->storeAs('videos', $this->video_id . '.' . $extension, 'azure');
        $video->update(['video_url' => $videoPath]);

        // Move to the next step (Combined metadata, logo, subtitle, and thumbnail upload)
        $this->currentStep = 2;
    }

    // Handles video metadata, logo, subtitle, and thumbnail in one submission
    public function submitVideoMetaAndOthers()
    {
        // Validate video metadata
        $this->validate(['videoname' => 'required|string|max:255']);

        $user = Auth::user();

        // Find the existing video entry
        $video = Video::find($this->video_id);

        // Update video metadata
        $video->update(['name' => $this->videoname]);

        if ($user->userplan == 'enterprise'){
            // Handle optional logo upload
            if ($this->logo) {
                $this->validate(['logo' => 'image|max:1024']);
                $logoPath = $this->logo->storeAs('logos', $this->video_id . '.' . $this->logo->getClientOriginalExtension(), 'azure');
                $video->update(['logo_url' => $logoPath]);
            }

            // Handle optional subtitle upload
            if ($this->subtitle) {
                $this->validate(['subtitle' => 'file|max:2048']);
                $subtitlePath = $this->subtitle->storeAs('subtitles', $this->video_id . '.ass', 'azure');
                $video->update(['subtitle_url' => $subtitlePath]);
            }
        }

        // Handle optional thumbnail upload
        if ($this->thumbnail) {
            $this->validate(['thumbnail' => 'image|max:1024']);
            $thumbnailPath = $this->thumbnail->storeAs('thumbnails', $this->video_id . '.' . $this->thumbnail->getClientOriginalExtension(), 'public');
            $video->update(['thumbnail_url' => $thumbnailPath]);
        }

        // Finalize the video with status "Initiated"
        $video->update(['status' => 'Initiated']);

        // Redirect or trigger an event for success
        return redirect()->route('all-videos')->with('success', 'Video uploaded is being processed!');

    }

    public function render()
    {
        $user = Auth::user();
        return view('livewire.video-form', compact('user'));
    }

    private function getServerId()
    {
        return 1; // Simplified, ideally dynamic based on server load or user preference
    }
}
