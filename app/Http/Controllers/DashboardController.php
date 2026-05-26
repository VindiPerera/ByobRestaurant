<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $modules = $role->modules()->get();

        return view('dashboard.index', [
            'user' => $user,
            'role' => $role,
            'modules' => $modules,
        ]);
    }
}
