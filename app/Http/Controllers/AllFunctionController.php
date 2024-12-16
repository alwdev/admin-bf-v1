<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AllFunctionController extends Controller
{
    //
    public static function strRandom($length)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($str, 5)), 0, $length);
    }
}
