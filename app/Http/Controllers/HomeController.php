<?php

namespace App\Http\Controllers;

use App\Models\Headache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $lastHeadaches = Headache::where("user_id", Auth::id())
            ->limit(5)
            ->orderBy("date", "desc")
            ->orderBy("time", 'desc')
            ->get();
        return view('home', compact('lastHeadaches'));
    }
}
