<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class CheckoutController extends Controller
{
    public function index()
    {

        // Muestra la vista con el formulario
        return view('pages.checkout.index');
    }

    public function success()
    {

        // Muestra la vista con el formulario
        return view('pages.checkout.success');
    }

}
