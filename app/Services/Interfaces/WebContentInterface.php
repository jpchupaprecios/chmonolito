<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface WebContentInterface
{
	public static function scrape(string $url, ?string $cookie = null, $userAgent = "", bool $clean = true): string|array|false;

	public static function getWebContent($query): mixed;

	public static function getUserAgent(): string;

	public static function parseUrlQuery($url): array;

	public static function getHeaders(?string $cookie, string $userAgent): array;
}
