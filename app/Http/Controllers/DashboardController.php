<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role->name === 'admin') {
            return view('dashboard.jefe');
        }

        if ($user->role->name === 'jefe_bodega') {
            return view('dashboard.jefe');
        }

        if ($user->role->name === 'bodeguero') {
            return redirect()->route('bodega.index');
        }

        abort(403);
    }
}
