<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class ProductController extends Controller
{
    public function index($id)
    {
        // Muestra la vista con el formulario
        return view('pages.details.index');
    }
}
