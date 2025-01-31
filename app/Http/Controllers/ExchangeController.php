<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Exchange;
final class ExchangeController extends Controller
{
    /**
     * @var $exchange
     */
    private $exchange;

    public function __construct(Request $request)
    {

    }

    public function syncExchange(){
        Price::syncExchange();

        return $this;
    }


}
