<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $promotions = Promotion::where('active',1)->get();
        return view('promotion.index',compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('promotion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $pro = new Promotion;
        $pro->name = $request->name;
        $pro->turnover = $request->turnover;
        $pro->deposit = $request->deposit;
        $pro->bonus = $request->bonus;
        if(isset($request->enable)){
            $pro->enable = $request->enable;
        }else{
            $pro->enable = 0;
        }
        if(isset($request->is_newuser)){
            $pro->is_newuser = $request->is_newuser;
        }else{
            $pro->is_newuser = 0;
        }
        $pro->description = $request->description;
        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $pro->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        $pro->withdraw_limit = $request->withdraw_limit;
        $pro->active = 1;
        $pro->save();
        return redirect()->route('promotion.index')->with('status','200');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $promotion = Promotion::find($id);
        return view('promotion.edit',compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $pro = Promotion::find($id);
        $pro->name = $request->name;
        $pro->turnover = $request->turnover;
        $pro->deposit = $request->deposit;
        $pro->bonus = $request->bonus;
        if(isset($request->enable)){
            $pro->enable = $request->enable;
        }else{
            $pro->enable = 0;
        }
        if(isset($request->is_newuser)){
            $pro->is_newuser = $request->is_newuser;
        }else{
            $pro->is_newuser = 0;
        }
        $pro->description = $request->description;
        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $pro->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        $pro->withdraw_limit = $request->withdraw_limit;
        $pro->active = 1;
        $pro->save();
        return redirect()->route('promotion.index')->with('status','200');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $pro = Promotion::find($request->id);
        $pro->active = 0;
        $pro->save();
        return redirect()->route('promotion.index')->with('status','200');
    }
}
