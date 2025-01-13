<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('created_at', 'DESC')
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function latestArticles(Request $request)
    {
        $articles = Article::orderBy('created_at', 'DESC')
            ->where('status', 1)
            ->limit($request->limit)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function article($id)
    {
        $article = Article::find($id);

        if ($article == null) {
            return response()->json([
                'status' => false,
                'message' => 'Böyle bir gönderi bulunamadı'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $article,
        ]);
    }
}
