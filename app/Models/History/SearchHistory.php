<?php

declare(strict_types=1);

namespace App\Models\History;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SearchHistory extends Model
{
	use HasFactory;

	protected $table = 'search_history';

}
