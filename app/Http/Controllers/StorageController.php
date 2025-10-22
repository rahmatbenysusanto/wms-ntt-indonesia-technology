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
        $storage = Storage::where('deleted_at', null)->paginate(10);

        $raw = Storage::whereNull('area')->whereNull('rak')->whereNull('bin')->get();

        $title = 'Storage';
        return view('storage.index', compact('title', 'storage', 'raw'));
    }

    public function getArea(Request $request): \Illuminate\Http\JsonResponse
    {
        $area = Storage::where('raw', $request->get('raw'))
            ->where('area', '!=', null)
            ->where('rak', null)
            ->where('bin', null)
            ->get();

        return response()->json([
            'data' => $area
        ]);
    }

    public function getRak(Request $request): \Illuminate\Http\JsonResponse
    {
        $area = Storage::where('raw', $request->get('raw'))
            ->where('area', $request->get('area'))
            ->where('rak', '!=', null)
            ->where('bin', null)
            ->get();

        return response()->json([
            'data' => $area
        ]);
    }

    public function getBin(Request $request): \Illuminate\Http\JsonResponse
    {
        $area = Storage::where('raw', $request->get('raw'))
            ->where('area', $request->get('area'))
            ->where('rak', $request->get('rak'))
            ->where('bin', '!=', null)
            ->get();

        return response()->json([
            'data' => $area
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        switch ($request->post('type')) {
            case 'raw':
                Storage::create([
                    'raw' => $request->post('raw'),
                ]);
                break;
            case 'area':
                Storage::create([
                    'raw'   => $request->post('raw'),
                    'area'  => $request->post('area'),
                ]);
                break;
            case 'rak':
                Storage::create([
                    'raw'   => $request->post('raw'),
                    'area'  => $request->post('area'),
                    'rak'   => $request->post('rak'),
                ]);
                break;
            case 'bin':
                Storage::create([
                    'raw'   => $request->post('raw'),
                    'area'  => $request->post('area'),
                    'rak'   => $request->post('rak'),
                    'bin'   => $request->post('bin'),
                ]);
        }

        return redirect()->back()->with('success', 'Storage created successfully');
    }

    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        Storage::where('id', $request->get('id'))->update(['deleted_at' => now()]);

        return response()->json([
            'status' => true,
        ]);
    }

    public function raw(): View
    {
        $storage = Storage::whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->where('deleted_at', null)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->paginate(10);

        $title = 'Storage Raw';
        return view('storage.raw', compact('title', 'storage'));
    }

    public function area(): View
    {
        $storage = Storage::whereNull('rak')
            ->whereNull('bin')
            ->whereNotNull('area')
            ->where('deleted_at', null)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->whereNull('deleted_at')
            ->paginate(10);

        $raw = Storage::whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->whereNotIn('id', [1, 2, 3, 4])
            ->whereNull('deleted_at')
            ->get();

        $title = 'Storage Area';
        return view('storage.area', compact('title', 'storage', 'raw'));
    }

    public function rak(): View
    {
        $storage = Storage::whereNull('bin')
            ->whereNotNull('rak')
            ->where('deleted_at', null)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->paginate(10);

        $raw = Storage::whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->whereNotIn('id', [1, 2, 3, 4])
            ->whereNull('deleted_at')
            ->get();

        $title = 'Storage Rak';
        return view('storage.rak', compact('title', 'storage', 'raw'));
    }

    public function bin(): View
    {
        $storage = Storage::whereNotNull('bin')
            ->where('deleted_at', null)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->paginate(10);

        $raw = Storage::whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->whereNotIn('id', [1, 2, 3, 4])
            ->whereNull('deleted_at')
            ->get();

        $title = 'Storage Bin';
        return view('storage.bin', compact('title', 'storage', 'raw'));
    }
}
