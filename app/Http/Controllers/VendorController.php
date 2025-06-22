<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(): View
    {
        $vendor = Vendor::where('deleted_at', null)->paginate(10);

        $title = 'Vendor';
        return view('vendor.index', compact('title', 'vendor'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Vendor::create([
            'name'  => $request->post('name')
        ]);

        return redirect()->back()->with('success', 'Vendor created successfully.');
    }

    public function findJSON(Request $request): \Illuminate\Http\JsonResponse
    {
        $vendor = Vendor::find($request->get('id'));

        return response()->json([
            'data' => $vendor
        ]);
    }

    public function edit(Request $request): \Illuminate\Http\RedirectResponse
    {
        Vendor::find($request->post('id'))
            ->update([
                'name'          => $request->post('name'),
                'updated_at'    => now()
            ]);

        return redirect()->back()->with('success', 'Vendor updated successfully.');
    }

    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        Vendor::find($request->query('id'))
            ->update([
                'deleted_at' => now()
            ]);

        return redirect()->back()->with('success', 'Vendor deleted successfully.');
    }
}
