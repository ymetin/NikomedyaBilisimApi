<?php

use App\Models\TempImage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $temp_images = TempImage::all();
    if (count($temp_images) > 0) {
        foreach ($temp_images as $row) {
            File::delete(public_path('uploads/temp/' . $row->name));
            File::delete(public_path('uploads/temp/thumb/' . $row->name));
            TempImage::find($row->id)->delete();
        }
    }
})->daily();
