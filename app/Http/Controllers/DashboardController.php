<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $employee = Employee::count();
        $user = User::count();

        return view('dashboard.home', compact('title', 'user', 'employee'));
    }
}
