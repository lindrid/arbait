<?php

namespace App\Http\Controllers;

use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CUploadImage extends Controller
{
    use UploadTrait;

    public function do(Request $request)
    {
        $request->validate(
            [
                'passport_image'    =>  'required|image|mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'passport_image.image' => "Загружайте изображение, а не иной файл!"
            ]
        );

        if ($request->has('passport_image')) {
            // Get image file
            $image = $request->passport_image;
            // Make a image name based on user name and current timestamp
            $name = str_slug($request->phone_call).'_'.time();
            // Define folder path
            $folder = '/uploads/images/';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = $folder . $name. $image->getClientOriginalExtension();
            // Upload image
            $this->storeFile($image, $folder, 'public', $name);
            // Set user profile image path in database to filePath
            //$user->profile_image = $filePath;
            Session::put('passport_image_file_path', $filePath);
            Session::save();
        }
    }
}
