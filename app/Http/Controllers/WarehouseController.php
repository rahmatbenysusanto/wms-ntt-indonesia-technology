<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouse = Warehouse::all();

        $title = 'Warehouse';
        return view('warehouse.index', compact('warehouse', 'title'));
    }
}
