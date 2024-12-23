<?php

declare(strict_types=1);

namespace App\Models\Search\Pagination;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

final class Pagination extends Model
{
	use HasFactory;

	protected $table = 'pagination';

	protected $guarded = [];

	protected $fillable = ['current', 'totalPages', 'next', 'prev'];

	public function links(): HasMany
	{
		return $this->hasMany(PaginationLink::class);
	}

	public function getData(): array
	{
		$links = [];
		try {
			$linksModel = $this->getRelation('links');
			foreach ($linksModel as $link) {
				$links[] = $link->getData();
			}

			$next = $this->getAttribute('next');
			$prev = $this->getAttribute('prev');

			if (is_resource($next)) {
				$next = stream_get_contents($next);
			}

			if (is_resource($prev)) {
				$prev = stream_get_contents($prev);
			}

			$totalPages = $this->getAttribute('totalPages');

			if (!$totalPages) {
				if ($next || $prev) {
					$totalPages = $this->getAttribute('current') + 1;
				}
			}

			return [
				'current' => $this->getAttribute('current'),
				'totalPages' => $totalPages,
				'next' => ($this->getAttribute('next')) ? $next : '',
				'prev' => ($this->getAttribute('prev')) ? $prev : '',
				'links' => $links,
			];
		} catch (Exception $e) {
			Log::debug('Error Pagination links');
			Log::debug('Request: ' . json_encode($_REQUEST));
			return [
				'current' => 1,
				'totalPages' => 0,
				'next' => '',
				'prev' => '',
				'links' => [],
			];
		}

		return [
			'current' => 1,
			'totalPages' => 0,
			'next' => '',
			'prev' => '',
			'links' => [],
		];
	}
}
