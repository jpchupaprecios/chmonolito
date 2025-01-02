<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class IndexController extends Controller
{
    public function index()
    {

        // Muestra la vista con el formulario
        return view('pages.myaccount.index');
    }
}
