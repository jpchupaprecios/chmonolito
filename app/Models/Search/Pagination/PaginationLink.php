<?php

declare(strict_types=1);

namespace App\Models\Search\Pagination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PaginationLink extends Model
{
	use HasFactory;

	protected $fillable = ['link', 'title', ];

	public function pagination()
	{
		return $this->belongsTo(Pagination::class);
	}

	public function getData(): array
	{
		$link = $this->getAttribute('link');
		$title = $this->getAttribute('title');

		if (is_resource($link)) {
			$link = stream_get_contents($link);
		}

		return [
			'link' => $link,
			'title' => $title,
		];
	}
}
