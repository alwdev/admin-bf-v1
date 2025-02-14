<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    //
    public function edit(){
        $store = Store::where('id',Auth::user()->store_id)->first();
        
        return view('company.edit', compact('store'));
    }
    public function saveconfigure(Request $request)
    {   
        // dd($request);
  
        $validated = $request->validate([
            'name' => ['required'],
            'website' => ['unique:store,website,'.Auth::user()->store_id],
            
            //'convenience_fee' => ['numeric']
        ]);

        $store =  Store::where('id',Auth::user()->store_id)->first();
        $store->name = $request->name;
        $store->email = $request->email;
        $store->phone1 = $request->phone1;
        $store->address = $request->address;
        $store->secret_key = $request->secret_key;
        $store->API_key = $request->API_key;
        $store->auth_key = $request->auth_key;
        $store->website = $request->website;
        $store->domain = $request->domain_name;
        $store->merchantID = $request->merchantID;
        $store->save();

        return redirect()->route('company.store.edit')->with('success', 'Update Done');
        
    }

    public function uploadstoreimage(Request $request)
    {
        
        $validated = $request->validate([
            'storeimgupload' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
  
        $fileName = auth()->user()->store_id.'.'.$request->storeimgupload->extension();  
        $request->storeimgupload->move(public_path('images/storeimages'), $fileName);
        $store = Store::find(auth()->user()->store_id);
        $store->image = "images/storeimages/".$fileName;
        $store->save();
      
        return response()->json($store->image);
    }

}
