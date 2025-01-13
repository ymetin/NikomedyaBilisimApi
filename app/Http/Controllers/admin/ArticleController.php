<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug'
        ], [
            'title.required' => 'Makale başlığı gereklidir.',
            'slug.required' => 'Slug gereklidir.',
            'slug.unique' => 'Böyle bir makale zaten var'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $article = new Article();
        $article->title = $request->title;
        $article->author = $request->author;
        $article->slug = Str::slug($request->slug);
        $article->content = $request->content;
        $article->status = $request->status;
        $article->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $article->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/articles/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(450, 300);
                $image->save($destPath);

                //Create large thumbnail
                $destPath = public_path('uploads/articles/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Makale başarıyla eklendi.',
        ]);
    }

    public function show($id)
    {
        $article = Article::find($id);

        if ($article == null) {
            return response()->json([
                'status' => false,
                'message' => 'Makale bulunamadı.'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $article
        ]);
    }

    public function update(Request $request, $id)
    {

        $article = Article::find($id);

        if ($article == null) {
            return response()->json([
                'status' => false,
                'message' => 'Makale bulunamadı.'
            ]);
        }

        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug,' . $id . ',id'
        ], [
            'title.required' => 'Makale başlığı gereklidir.',
            'slug.required' => 'Slug gereklidir.',
            'slug.unique' => 'Böyle bir makale zaten var'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $article->title = $request->title;
        $article->author = $request->author;
        $article->slug = Str::slug($request->slug);
        $article->content = $request->content;
        $article->status = $request->status;
        $article->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $oldImage = $article->image;
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $article->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/articles/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(450, 300);
                $image->save($destPath);

                //Create large thumbnail
                $destPath = public_path('uploads/articles/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();

                if ($oldImage != '') {
                    File::delete(public_path(('uploads/articles/large/' . $oldImage)));
                    File::delete(public_path(('uploads/articles/small/' . $oldImage)));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Makale başarıyla güncellendi.',
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if ($article == null) {
            return response()->json([
                'status' => false,
                'message' => 'Makale bulunamadı.'
            ]);
        }

        if ($article->image != '') {
            File::delete(public_path(('uploads/articles/large/' . $article->image)));
            File::delete(public_path(('uploads/articles/small/' . $article->image)));
        }

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'Makale başarıyla silindi.'
        ]);
    }
}
