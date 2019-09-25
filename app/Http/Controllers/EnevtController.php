<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnevtController extends Controller
{
    public function event()
    {
    	echo $_GET['echostr'];
    }
}
