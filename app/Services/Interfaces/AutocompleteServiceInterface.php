<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface AutocompleteServiceInterface
{
	/**
	 * Obtiene sugerencias de autocompletado basadas en una consulta.
	 *
	 * @param string $query La consulta para autocompletar.
	 * @return array Una lista de sugerencias.
	 */
	public function getSuggestions(string $query): array;
}
