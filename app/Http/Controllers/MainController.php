<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Show the homepage
     */
    public function index()
    {
        return view('main.index');
    }

    /**
     * Show the store page
     */
    public function store()
    {
        return view('main.store');
    }

    /**
     * Show categories page
     */
    public function categories()
    {
        return view('main.categories');
    }

    /**
     * Show single game detail
     */
    public function gameDetail($id)
    {
        return view('main.game-detail', ['gameId' => $id]);
    }

    /**
     * Show login page
     */
    public function login()
    {
        return view('main.auth.login');
    }

    /**
     * Show register page
     */
    public function register()
    {
        return view('main.auth.register');
    }

    /**
     * Handle auth callback from social login
     */
    public function authCallback()
    {
        return view('main.auth.callback');
    }
}

