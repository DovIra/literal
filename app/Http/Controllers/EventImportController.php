<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventImportController extends Controller
{
    public function showImportForm()
    {
        return view('events.import');
    }

    public function import(Request $request)
    {
        
    }
}
