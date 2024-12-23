<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Carousel extends Model
{
	use HasFactory;

	protected $fillable = ['query', 'vendor', 'results'];

	protected $table = 'carousel';

	public function getData(): array
	{
		return [
			'query' => $this->query,
			'vendor' => $this->vendor,
			'results' => json_decode($this->results),
		];
	}
}
