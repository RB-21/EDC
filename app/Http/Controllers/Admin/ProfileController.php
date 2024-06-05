<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        return view('admin.profile.index');
    }

    public function edit(Request $request){
        if(empty(auth()->user())){
            return redirect()->route('login');
        }
        $user = User::findOrFail(auth()->user()->id);
        $request->validate([
            'password' => 'required|min:8|max:15'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with([
            'success' => true,
            'message' => 'Berhasil Mengupdate Password'
        ]);
    }
}
