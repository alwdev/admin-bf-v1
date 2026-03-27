<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

/**
 * อัปโหลดรูป AMB ใต้ public/images/... แบบเดียวกับ ProviderController (ระบบเดิม)
 */
class AmbImageUpload
{
    public static function saveProductImage(UploadedFile $file, int $productId): string
    {
        $dir = public_path('images/amb/products');
        File::makeDirectory($dir, 0755, true, true);
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $fileName = 'ambprod'.$productId.'.'.$ext;
        $file->move($dir, $fileName);

        return '/images/amb/products/'.$fileName;
    }

    public static function saveGameImage(UploadedFile $file, int $gameId): string
    {
        $dir = public_path('images/amb/games');
        File::makeDirectory($dir, 0755, true, true);
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $fileName = 'ambgame'.$gameId.'.'.$ext;
        $file->move($dir, $fileName);

        return '/images/amb/games/'.$fileName;
    }
}
