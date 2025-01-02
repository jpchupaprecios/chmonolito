<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class CartController extends Controller
{
    public function index()
    {

        // Muestra la vista con el formulario
        return view('pages.cart.index');
    }
}
