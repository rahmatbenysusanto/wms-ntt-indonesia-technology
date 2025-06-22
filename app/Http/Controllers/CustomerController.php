<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customer = Customer::where('deleted_at', null)->paginate(10);

        $title = 'Customer';
        return view('customer.index', compact('title', 'customer'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Customer::create([
            'name'      => $request->post('name'),
            'provinsi'  => $request->post('provinsi'),
            'kota'      => $request->post('kota'),
            'kode_post' => $request->post('kode_post'),
        ]);

        return redirect()->back()->with('success', 'Create Customer Successfully');
    }
}
