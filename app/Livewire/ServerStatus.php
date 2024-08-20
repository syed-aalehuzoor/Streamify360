<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;

class ServerStatus extends Component
{
    public $serverId;
    public $status;

    protected $listeners = ['refreshStatus' => '$refresh'];

    public function mount($serverId)
    {
        $this->serverId = $serverId;
        $this->status = Server::find($this->serverId)->status;
    }

    public function updateStatus()
    {
        $server = Server::find($this->serverId);

        // Example logic to update the status. Replace with actual logic.
        if ($server->status != 'live') {
            $this->status = $server->status;
        }
    }

    public function render()
    {
        $this->updateStatus();
        return view('livewire.server-status');
    }
}