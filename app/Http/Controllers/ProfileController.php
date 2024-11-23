<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read profile');
    }

    public function index()
    {
        $title = 'Profile';

        return view('dashboard.profiles.index', compact('title'));
    }
}
