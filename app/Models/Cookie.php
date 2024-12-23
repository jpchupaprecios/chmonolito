<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Cookie extends Model
{
	use HasFactory;

	protected $fillable = ['vendor', 'cookies'];

	protected $table = 'cookies';
    private string $cookies;


    public function getData(): array
	{
        $cookies = [];
        if($this->cookies){
            foreach(explode(";", $this->cookies) as $cookie){
                $cookie = explode("=", $cookie);
                $cookies[$cookie[0]] = $cookie[1];
            }

            return $cookies;
        }

		return [];
	}
}
