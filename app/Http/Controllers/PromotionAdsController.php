<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromotionAds;

class PromotionAdsController extends Controller
{
    //
    public function index(){
        $ads = PromotionAds::where('active',1)->get();
        return view('ads.index',compact('ads'));
    }
    public function create(){
        return view('ads.create');
    }
    public function store(Request $request){
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $ads = new PromotionAds();
        $ads->title = $request->title;
        $ads->description = $request->description;

        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $ads->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        if(isset($request->enable)){
            $ads->enable = $request->enable;
        }else{
            $ads->enable = 0;
        }
        $ads->save();
        return redirect()->route('setting.banner')->with('status','200');
    }
    public function edit($id){
        $ads = PromotionAds::find($id);
        return view('ads.edit',compact('ads'));
    }
    public function update(Request $request,$id){
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $ads = PromotionAds::find($id);
        $ads->title = $request->title;
        $ads->description = $request->description;

        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $ads->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        if(isset($request->enable)){
            $ads->enable = $request->enable;
        }else{
            $ads->enable = 0;
        }
        $ads->save();
        return redirect()->route('setting.banner')->with('status','200');
    }
    public function destroy(Request $request)
    {
        $ads = PromotionAds::find($request->id);
        $ads->active = 0;
        $ads->save();
        return redirect()->route('setting.banner')->with('status','200');
    }
}
