<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

class TestimonialController extends Controller
{
    //Return all testimonials
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $testimonials,
        ]);
    }

    //Return sigle testimoial
    public function show($id)
    {
        $testimonial = Testimonial::find($id);

        if ($testimonial == null) {
            return response()->json([
                'status' => false,
                'data' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $testimonial,
        ]);
    }

    //insert or store a testimoinal to db
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
        ], [
            'testimonial.required' => 'Bu alan gereklidir boş bırakılamaz',
            'citation.required' => 'Bu alan gereklidir boş bırakılamaz.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $testimonial = new Testimonial();
        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->designation = $request->designation;
        $testimonial->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $testimonial->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/testimonials/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(300, 300);
                $image->save($destPath);

                $testimonial->image = $fileName;
                $testimonial->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Müşteri görüşü başarıyla eklendi'
        ]);
    }

    //insert or store a testimoinal to db
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::find($id);

        if ($testimonial == null) {
            return response()->json([
                'status' => false,
                'data' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
        ], [
            'testimonial.required' => 'Bu alan gereklidir boş bırakılamaz',
            'citation.required' => 'Bu alan gereklidir boş bırakılamaz.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->designation = $request->designation;
        $testimonial->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $oldImage = $testimonial->image;
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $testimonial->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/testimonials/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(300, 300);
                $image->save($destPath);

                $testimonial->image = $fileName;
                $testimonial->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/testimonials/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Müşteri görüşü başarıyla güncellendi'
        ]);
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);

        if ($testimonial == null) {
            return response()->json([
                'status' => false,
                'data' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        if ($testimonial->image != '') {

            File::delete(public_path('uploads/testimonials/' . $testimonial->image));
        }

        $testimonial->delete();

        return response()->json([
            'status' => true,
            'message' => 'Müşteri görüşü başarıyla silindi'
        ]);
    }
}
