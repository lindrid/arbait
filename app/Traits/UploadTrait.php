<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 08.07.2019
 * Time: 11:40
 */

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function storeFile(UploadedFile $uploadedFile, $folder = null,
                              $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : str_random(25);

        $file = $uploadedFile->storeAs($folder, $name . '.' .
            $uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }
}