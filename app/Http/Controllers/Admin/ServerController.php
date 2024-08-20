<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ServerController extends Controller
{   

    public function index()
    {        
        $servers = Server::paginate(10);


        return view('admin.servers-table', [
            'servers' => $servers,
        ]);
    }

    public function create()
    {
        return view('admin.add-servers');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data with conditional rules
        $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|ip',
            'ssh_port' => 'required|integer',
            'username' => 'required|string|max:255',
            'type' => 'required|in:encoder,storage',
            'domain' => [
                'nullable',
                'regex:/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,}$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'storage' && empty($value)) {
                        $fail('The domain field is required when type is storage.');
                    }
                },
            ],
            'limit' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'encoder' && is_null($value)) {
                        $fail('The limit field is required when type is encoder.');
                    }
                },
            ],
        ]);
    
        // Check if a server with the same IP already exists
        $existingServer = Server::where('ip', $request->ip)->first();
    
        if ($existingServer) {
            return redirect()->route('admin-servers')->withErrors(['ip' => 'A server with this IP address already exists.']);
        }
    
        // Prepare data for server creation
        $serverData = [
            'name' => $request->name,
            'ip' => $request->ip,
            'ssh_port' => $request->ssh_port,
            'username' => $request->username,
            'domain' => $request->domain,
            'status' => 'pending', // Initial status
            'type' => $request->type,
            'total_videos' => 0,
        ];
    
        // Add 'limit' only if it's not null
        if ($request->type === 'encoder') {
            $serverData['limit'] = $request->limit;
        }
    
        // Create the server
        $server = Server::create($serverData);
    
        // Prepare and run the process to execute the Python script
        $startkey = env('START_KEY');
        $scriptPath = '../scripts/setup_server.py';
        $serverId = $server->id;
        $command = "python {$scriptPath} --id {$serverId}";
    
        $process = new Process([$startkey, 'python', $scriptPath, '--id', $serverId],
            null,
            ['SYSTEMROOT' => getenv('SYSTEMROOT'), 'PATH' => getenv("PATH")]);
    
        $process->run();
    
        return redirect()->route('admin-servers')->with('success', 'Server added successfully.');
    }
}
