<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $members,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ], [
            'name.required' => 'İsim alanı gereklidir',
            'job_title.required' => 'Meslek başlığı gereklidir'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $member = new Member();
        $member->name = $request->name;
        $member->job_title = $request->job_title;
        $member->linkedin_url = $request->linkedin_url;
        $member->status = $request->status;
        $member->save();
        //Save Temp Image
        if ($request->imageId > 0) {
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $member->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/members/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(300, 300);
                $image->save($destPath);

                $member->image = $fileName;
                $member->save();
            }
        }
        return response()->json([
            'status' => true,
            'message' => "Üye başarıyla eklendi."
        ]);
    }

    public function show($id)
    {
        $member = Member::find($id);

        if ($member == null) {
            return response()->json([
                'status' => false,
                'data' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $member,
        ]);
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if ($member == null) {
            return response()->json([
                'status' => false,
                'data' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ], [
            'name.required' => 'İsim alanı gereklidir',
            'job_title.required' => 'Meslek başlığı gereklidir'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $member->name = $request->name;
        $member->job_title = $request->job_title;
        $member->linkedin_url = $request->linkedin_url;
        $member->status = $request->status;
        $member->save();

        //Save Temp Image
        if ($request->imageId > 0) {
            $oldImage = $member->image;
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $member->id . '.' . $ext;

                //create small thumbnail
                $sourcePath = public_path('uploads/temp/' .  $tempImage->name);
                $destPath = public_path('uploads/members/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->cover(300, 300);
                $image->save($destPath);

                $member->image = $fileName;
                $member->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/members/' . $oldImage));
                }
            }
        }
        return response()->json([
            'status' => true,
            'message' => "Üye başarıyla güncellendi."
        ]);
    }

    public function destroy($id)
    {
        $member = Member::find($id);

        if ($member == null) {
            return response()->json([
                'status' => false,
                'message' => 'Böyle bir müşteri görüşü bulunamadı',
            ]);
        }

        if ($member->image != '') {
            File::delete(public_path('uploads/members/' . $member->image));
        }

        $member->delete();

        return response()->json([
            'status' => true,
            'message' => 'Üye başarıyla silindi.'
        ]);
    }
}
