<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardCustomerController extends Controller
{
    public function index(): View
    {
        $title = 'Dashboard Customer';
        return view('dashboard-customer.index', compact('title'));
    }
}
