<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapingSession extends Model
{
    use HasFactory;

    protected $table = 'scraping_sessions';

    /**
     * Los atributos que se pueden asignar masivamente (fillable).
     */
    protected $fillable = [
        'client_session_id',
        'amazon_cookie',
        'user_agent',
    ];


}
