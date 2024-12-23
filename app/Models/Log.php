<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Log extends Model
{
	use HasFactory;

	protected $fillable = [
        'message',
        'context',
        'level',
        'level_name',
        'channel',
        'record_datetime',
        'extra',
        'formatted',
        'remote_addr',
        'user_agent',
    ];

	protected $table = 'logs';
}
