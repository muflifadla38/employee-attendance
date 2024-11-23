<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read employee');
    }

    public function index()
    {
        $title = 'Employee';
        $roles = Role::get(['id', 'name']);

        return view('dashboard.employees.index', compact('title', 'roles'));
    }
}
