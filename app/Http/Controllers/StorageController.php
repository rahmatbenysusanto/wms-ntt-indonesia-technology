<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StorageController extends Controller
{
    public function index(): View
    {
        $storage = Storage::where('deleted_at', null)
            ->whereNot('raw', null)
            ->whereNot('area', null)
            ->whereNot('level', null)
            ->paginate(10);

        $title = 'Storage';
        return view('storage.index', compact('title', 'storage'));
    }
}
