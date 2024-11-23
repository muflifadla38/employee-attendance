<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read user');
    }

    public function index()
    {
        $title = 'Users';
        $roles = Role::get(['id', 'name']);

        return view('dashboard.users.index', compact('title', 'roles'));
    }
}
