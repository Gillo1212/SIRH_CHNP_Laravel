<?php

namespace App\Http\Controllers;

class AideController extends Controller
{
    public function index()
    {
        return view('aide.index');
    }

    public function faq()
    {
        return view('aide.faq');
    }

    public function guide()
    {
        return view('aide.guide');
    }

    public function raccourcis()
    {
        return view('aide.raccourcis');
    }
}
