<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Auth;
use \Crypt;

class ManageUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $userLogin = Auth::user();
        if ($userLogin->level==0){
            $alluser = User::where('active', 1)->whereIn('level',[1,2])->get();
        }elseif($userLogin->level==1){
            $alluser = User::where('active', 1)->where('level',2)->get();
        }else{
            $alluser = User::where('active', 1)->where('level',3)->get();
        }
        return view('manage-user.index',compact('alluser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $password  = app('App\Http\Controllers\AllFunctionController')->strRandom(8);
        return view('manage-user.add-user',compact('password'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        // dd($request->password);
        $u = new User();
        $u->name = $request->name;
        $u->email = $request->email;
        $u->level = (int) $request->level;
        $u->password = $request->password;
        $u->truepass = $request->password;
        $u->uu = auth()->user()->id;

        if((int) $request->level==2){
            //permissions staff
            $u->permissions = '{"member":"4","manageuser":"0","setting":"0","transfer":"4","report":"4"}';
        }else{
            $u->permissions = '{"member":"4","manageuser":"4","setting":"4","transfer":"4","report":"4"}';
        }
        $u->save();

        return redirect()->route('manageuser.index')->with('status','200');
    }

    function checkPassword(Request $request){
        if (Hash::check($request->password[0], Auth::user()->password)) {
            $u = User::find($request->userId[0]);
            return $u;
        } else {
            return false;
        }
    }
    function changePassword(Request $request){
        $u = User::find($request->userId[0]);
        $u->password = $request->password[0];
        $u->truepass = $request->password[0];
        $u->save();
        return $u;
    }
    function deluser(Request $request){

        $u = User::find($request->userid);
        $u->active = 0;
        $u->save();
        return redirect()->route('manageuser.index')->with('del_status','200');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $id = User::true_id($id);
        $user = User::find($id);
        $user->permissions =  json_decode($user->permissions);
        return view('manage-user.setpermission',compact('user'));
    }

    public function setPermission(Request $request)
    {
        //
        $id = User::true_id($request->userid);
        $user = User::find($id);
        $permissions = [];
        $permissions["member"] = $request->member;
        $permissions["manageuser"] = $request->manageuser;
        $permissions["transfer"] = $request->transfer;
        $permissions["report"] = $request->report;
        $user->permissions =  json_encode($permissions);
        $user->save();
        return redirect()->route('manageuser.show',$request->userid)->with('status','200');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
