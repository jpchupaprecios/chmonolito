<?php

declare(strict_types=1);

namespace App\Models\History;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class UserCart extends Model
{
    use HasFactory;

    protected $table = 'user_cart';

}

