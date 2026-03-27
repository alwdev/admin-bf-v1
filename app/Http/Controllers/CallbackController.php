<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    public function jili(Request $request){
        Log::info('jili callback'.$request->getContent());
        return response()->json(['code'=>200,'msg'=>'jili success']);
    }

    public function jdb(Request $request){
        Log::info('jdb callback'.$request->getContent());
        return response()->json(['code'=>200,'msg'=>'jdb success']);
    }

    public function tf(Request $request){
        Log::info('tf callback'.$request->getContent());
        return response()->json(['code'=>200,'msg'=>'tf success']);
    }
}
