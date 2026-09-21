<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'rolesCount' => Role::count(),
            'permissionsCount' => Permission::count(),
            'usersCount' => User::count(),
            'recentRoles' => Role::latest()->take(5)->get(),
        ]);
    }
}