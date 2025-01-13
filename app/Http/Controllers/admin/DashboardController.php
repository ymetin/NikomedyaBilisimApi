<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Setting;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function settings()
    {
        $setting = Setting::take(1)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $setting,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'gsm' => 'required',
            'email' => 'required|email'
        ], [
            'name.required' => 'Şirket ünvanı gereklidir.',
            'gsm.required' => 'Şirket telefonu gereklidir.',
            'email.required' => 'Şirket email adresi gereklidir.',
            'email.email' => 'Geçersiz e-mail adresi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $setting = Setting::take(1)->first();

        $setting->name = $request->name;
        $setting->phone = $request->phone;
        $setting->gsm = $request->gsm;
        $setting->email = $request->email;
        $setting->address = $request->address;
        $setting->site_meta_keywords = $request->site_meta_keywords;
        $setting->site_meta_description = $request->site_meta_description;
        $setting->facebook = $request->facebook;
        $setting->instagram = $request->instagram;
        $setting->linkedin = $request->linkedin;
        $setting->twitter = $request->twitter;
        $setting->youtube = $request->youtube;
        $setting->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $oldImage = $setting->logo;
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $setting->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/settings/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scale(150, 150);
                $image->save($destPath);

                $setting->logo = $fileName;
                $setting->save();

                if ($oldImage != '') {
                    File::delete(public_path(('uploads/settings/' . $oldImage)));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Ayarlar başarıyla güncellendi.',
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

    public function updateAbout(Request $request)
    {
        $about = AboutUs::take(1)->first();

        $about->title = $request->title;
        $about->body = $request->body;
        $about->save();

        if ($request->imageId > 0) {
            $oldImage = $about->image;
            $tempImage = TempImage::find($request->imageId);

            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $about->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/about/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scale(600, 370);
                $image->save($destPath);

                $about->image = $fileName;
                $about->save();

                if ($oldImage != '') {
                    File::delete(public_path(('uploads/about/' . $oldImage)));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Hakkımızda sayfası başarıyla güncellendi.',
        ]);
    }
}
