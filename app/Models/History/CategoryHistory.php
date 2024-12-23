<?php

declare(strict_types=1);

namespace App\Models\History;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class CategoryHistory extends Model
{
	use HasFactory;

	protected $table = 'category_history';

}
