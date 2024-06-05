<?php

namespace App\Http\Middleware;

use App\Models\MasterUserRole;
use App\Models\ProhibitedPassword;
use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // $list_prohibited_password = ProhibitedPassword::lazy()->pluck('value');
        // if($request->password == auth()->user()->nik || in_array($request->password, $list_prohibited_password->toArray())){
        //     return redirect()->route('update_password')->with('update_password', true);
        // }
        $master_user_role = MasterUserRole::lazy();
        foreach($master_user_role as $user_role){
            if($user_role->nama == $role && auth()->user()->uRole->nama == $role){
                return $next($request);
            }
        }
        return abort(404);
    }
}
