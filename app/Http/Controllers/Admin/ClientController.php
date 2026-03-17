<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ClientController extends Controller
{
    public function index()
    {
        // Fetch only users with role = 'user'
        $clients = User::where('role', 'user')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.clients', compact('clients'));
    }
}
