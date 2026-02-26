<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyListController extends Controller
{
    class MyListController extends Controller
    {
    public function index()
    {
        $likedItems = auth()->user()->likedItems()->latest()->get();
        return view('mypage.mylist', compact('likedItems'));
    }
}
}
