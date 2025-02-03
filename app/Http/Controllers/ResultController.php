<?php

namespace App\Http\Controllers;

use App\Helpers\SymfonyPanther;
use App\Models\ScrapingSession;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use DOMDocument;
use DOMXPath;
use tidy;
use App\Parsers\Chapi\Amazon\Results\ChapiAmazonResultParser;
use App\Http\Controllers\BehaviorController;
class ResultController extends Controller
{

}
