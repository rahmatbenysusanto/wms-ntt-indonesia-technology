<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralRoomController extends Controller
{
    public function index(): View
    {
        $title = "General Room";
        return view('general-room.index', compact('title'));
    }
}
