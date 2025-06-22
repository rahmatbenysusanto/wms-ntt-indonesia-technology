<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageController extends Controller
{
    public function index(): View
    {
        $storage = Storage::where('deleted_at', null)->paginate(10);

        $title = 'Storage';
        return view('storage.index', compact('title', 'storage'));
    }
}
