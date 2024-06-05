<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use App\Models\User;
use App\Models\UserBiasaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        $user_jenis_file = MasterJenisFile::whereIn('kode', explode(',',auth()->user()->jenis_file))->pluck('singkatan')->join(' | ');
        $user_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->pluck('nama')->join(' | ');
        return view('operator.profile.index', compact('user_jenis_file', 'user_jenis_aksi'));
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

    public function edit_email(Request $request){
        $request->validate([
            'email' => 'required|unique:users,email'
        ]);
        $user = User::findOrFail(auth()->user()->id);
        $user->update([
            'email' => $request->email
        ]);
        return back()->with(['success' => true, 'message' => 'Berhasil Mengupdate Email']);
    }

    public function edit_nohp(Request $request){
        $request->validate([
            'nohp' => 'required|numeric'
        ]);
        $user = UserBiasaDetail::where('user_id', auth()->user()->id)->firstOrFail();
        $user->update([
            'no_hp' => $request->nohp
        ]);
        return back()->with(['success' => true, 'message' => 'Berhasil Mengupdate No HP']);
    }
}
