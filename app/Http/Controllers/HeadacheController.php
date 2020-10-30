<?php

namespace App\Http\Controllers;

use App\Models\Headache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadacheController extends Controller
{
    public function store()
    {
        $user_id = Auth::id();
        Headache::create([
            "user_id" => $user_id,
            "date" => \request("date"),
            "time" => \request("time"),
            "strength" => \request("strength"),
            "comments" => \request("comment",''),
        ]);
        return back();
    }
}
