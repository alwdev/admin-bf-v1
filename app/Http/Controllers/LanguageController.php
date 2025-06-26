<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function change(Request $request)
    {
        $lang = $request->lang;
        // dd($lang);
        if (!in_array($lang, ['en', 'th','lo','vn','ko','zh'])) {
            abort(400);
        }

        Session::put('locale', $lang);

        return redirect()->back();
    }
}
