<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors('image')
            ]);
        }

        if ($request->file('file')) {

            foreach ($request->file('file') as $key => $image) {

                $imageName = time() . rand(1, 99) . '.' . $image->extension();

                $image->move(public_path('uploads/gallery'), $imageName);

                //Create small thumbnail
                $sourcePath = public_path('uploads/gallery/' . $imageName);
                $destPath = public_path('uploads/gallery/small/' . $imageName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(300, 300);
                $image->save($destPath);



                $images[]['name'] = $imageName;
            }
        }

        foreach ($images as $key => $image) {

            Gallery::create($image);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Resim başarıyla kaydedildi.',
        ], 200);
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);

        if ($gallery == null) {
            return response()->json([
                'status' => false,
                'message' => 'Böyle bir resim bulunamadı',
            ]);
        }

        if ($gallery->name != '') {
            File::delete(public_path('uploads/gallery/' . $gallery->name));
            File::delete(public_path('uploads/gallery/small/' . $gallery->name));
        }

        $gallery->delete();

        return response()->json([
            'status' => true,
            'message' => 'Resim başarıyla silindi.'
        ]);
    }
}
