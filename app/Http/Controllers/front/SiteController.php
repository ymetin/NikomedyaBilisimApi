<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    //Site general information for frontend
    public function index()
    {
        $setting = Setting::take(1)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $setting,
        ]);
    }

    public function about_us()
    {
        $about = AboutUs::take(1)->get();

        return response()->json([
            'status' => true,
            'data' => $about
        ]);
    }
}
