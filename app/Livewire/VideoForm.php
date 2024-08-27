<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Video;
use App\Models\Server;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

class VideoForm extends Component
{
    use WithFileUploads;

    public $videoname;
    public $video;
    public $subtitle;
    public $logo;
    public $thumbnail;

    protected $rules = [
        'videoname' => 'nullable|string|max:2000',
        'video' => 'required|file|mimes:mp4,avi,mkv|max:512000', // 500 MB
        'subtitle' => 'nullable|file|mimes:srt,vtt|max:5000',
        'logo' => 'nullable|file|image|max:2000',
        'thumbnail' => 'nullable|file|image|max:2000',
    ];    

    private function getServerId()
    {
        $servers = Server::all();
        // Implement your logic to retrieve the server ID
        return 11; // Example static value, replace with actual logic
    }

    public function save()
    {
        $this->validate();

        $videoPath = $this->video->store('videos');
        $videoname = $this->videoname ?? pathinfo($this->video->getClientOriginalName(), PATHINFO_FILENAME);
        $subtitlePath = $this->subtitle ? $this->subtitle->store('subtitles') : null;
        $logoPath = $this->logo ? $this->logo->store('logos') : null;
        $thumbnailPath = $this->thumbnail ? $this->thumbnail->store('thumbnails') : null;

        $video = Video::create([
            'name' => $videoname,
            'userid' => Auth::id(),
            'serverid' => $this->getServerId(),
            'status' => 'Initiated',
            'thumbnail_url' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
            'subtitle_filepath' => $subtitlePath,
            'subtitle_url' => $subtitlePath ? Storage::url($subtitlePath) : null,
            'logo_filepath' => $logoPath,
            'video_filepath' => $videoPath,
        ]);

        $startkey = env('START_KEY');
        $pythonkey = env('PYTHON_KEY');
        $scriptPath = '../scripts/process_video.py';

        $process = new Process([$startkey, $pythonkey, $scriptPath, '--id', $video->id],
            null,
            ['SYSTEMROOT' => getenv('SYSTEMROOT'), 'PATH' => getenv("PATH")]);

        $process->run();

        return redirect()->route('all-videos')->with('success', 'Video uploaded successfully.');
    }

    public function render()
    {
        return view('livewire.video-form');
    }
}
