<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip',
        'ssh_port',
        'username',
        'domain',
        'status',
        'type',
        'limit',
        'total_videos',
    ];
}