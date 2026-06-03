<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CheckPermissionUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    /*
        Config NOTE
            "product" => permitproduct,
            "customer" => permitcustomer,
            "sale" => permitsale,
            "tax" => permittax,
            "report" => permitreport,
            "configure" => permitconfigure,
            "inventory"  => permitinventory,
    */
    public function handle(Request $request, Closure $next,$function,$action)
    {        

        $function_list = [
            "member"=>"permitmember",
            "manageuser"=>"permitmanageuser",
            "transfer"=>"permittransfer",
            "report"=>"permitreport",
            "setting"=>"permitsetting",
        ];
        $action_list = [
            "hide" => "1",
            "view" => "2",
            "edit" => "3",
            "delete" => "4",
        ];
        $requestName = $request->route()->getAction('as');
        $ignorelist = [
            // "packing",
        ];

        // $userrole = Auth::user()->level;
        // // dd( $userrole );
        // if( $userrole===0 && in_array($requestName,$ignorelist)){
        //     return $next($request);
        // }
        

        $permit = json_decode( auth()->user()->permissions );

        if($permit->$function < $action_list[$action]){
            
            return redirect('/');
        }

        // $permitfunction = $request->session()->get($function) ;
        // if($permitfunction < $action_list[$action]){
            
        //     return redirect('/');
        // }
        //END USE SESSION
       
        return $next($request);
    }
}