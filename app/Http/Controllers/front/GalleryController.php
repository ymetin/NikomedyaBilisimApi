<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::orderBy('created_at', 'DESC')
            ->where('status',1)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $images,
        ]);
    }
}
