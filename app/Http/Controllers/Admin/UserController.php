<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenHistory;
use App\Models\MasterBagian;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use App\Models\MasterUserLevel;
use App\Models\MasterUserRole;
use App\Models\User;
use App\Models\UserBiasaDetail;
use App\Models\UserTamuDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $master_bagian = MasterBagian::all();
        $master_user_level = MasterUserLevel::all();
        $master_user_role = MasterUserRole::all();
        $master_jenis_file = MasterJenisFile::all();
        $master_jenis_aksi = MasterJenisAksi::all();
        return view('admin.user.index', compact('master_bagian', 'master_user_level', 'master_user_role', 'master_jenis_file', 'master_jenis_aksi'));
    }

    public function simpan(Request $request)
    {
        $master_user_level = MasterUserLevel::pluck('level')->implode(',');
        $master_user_role = MasterUserRole::pluck('kode')->implode(',');
        $master_jenis_file = MasterJenisFile::pluck('kode')->implode(',');
        // dd($request->all(), $master_user_role);
        $request->validate([
            'nik' => 'required|unique:users,nik',
            // 'nik' => 'required|numeric',
            'nama' => 'required|min:3',
            'jabatan' => 'required',
            'nohp' => 'required|numeric|min_digits:10|max_digits:13',
            'role' => "required|in:$master_user_role",
            'bagian' => Rule::requiredIf($request->role == 'op' || $request->role == 'usr'),
            'instansi' => 'required_if:role,tmu',
            'kepentingan' => 'required_if:role,tmu',
            'level' => "required|in:$master_user_level",
            'jenis_file' => "required|array",
            'jenis_file' => "required|array",
        ], [
            'nik.required' => 'NIK Harus Diisi',
            'nik.unique' => 'NIK Sudah Terdaftar',
            'nik.numeric' => 'NIK Harus Berupa Angka',
            'nama.required' => 'Nama Harus Diisi',
            'nama.min' => 'Nama Harus Diisi Minimal :min Huruf',
            'jabatan.required' => 'Jabatan Harus Diisi',
            'nohp.required' => 'No HP Harus Diisi',
            'nohp.numeric' => 'No HP Harus Berupa Angka',
            'nohp.min_digits' => 'No HP Harus Berjumlah :min Angka, ',
            'nohp.max_digits' => 'No HP Harus Berjumlah :max Angka, ',
            'bagian.required' => 'Bagian Harus Diisi',
            'level.required' => 'Level Harus Diisi',
            'level.in' => 'Level Harus Dalam Jangka :values',
        ]);

        $user = User::create([
            'nik' => $request->nik,
            'name' => $request->nama,
            'password' => Hash::make($request->nik),
            'user_level' => $request->level,
            'jenis_file' => join(',', $request->jenis_file),
            'jenis_aksi' => join(',', $request->jenis_aksi),
            'active_from' => $request->active_from,
            'active_to' => $request->active_to,
            'active_status' => 1,
            'role' => $request->role
        ]);

        if ($user->role == 'tmu') {
            UserTamuDetail::create([
                'user_id' => $user->id,
                'no_hp' => $request->nohp,
                'instansi' => $request->instansi,
                'jabatan' => $request->jabatan,
                'kepentingan' => $request->kepentingan,
            ]);
        } elseif (in_array($user->role, ['op', 'usr'])) {
            UserBiasaDetail::create([
                'user_id' => $user->id,
                'no_hp' => $request->nohp,
                'bagian' => $request->bagian,
                'jabatan' => $request->jabatan
            ]);
        }
        return back()->with(['success' => true, 'message' => 'Berhasil Menambahkan User']);
    }

    public function edit(Request $request)
    {
        // dd($request->all());
        $master_user_level = MasterUserLevel::pluck('level')->implode(',');
        $master_user_role = MasterUserRole::pluck('kode')->implode(',');
        $master_jenis_file = MasterJenisFile::pluck('kode')->implode(',');
        $master_jenis_aksi = MasterJenisAksi::pluck('id')->implode(',');
        // dd($request->all(), $master_user_role);
        $request->validate([
            // 'nik' => 'required|unique:users,nik',
            // 'nik' => 'required|numeric',
            'nama' => 'required|min:3',
            'jabatan' => 'required',
            'nohp' => 'required|numeric|min_digits:10|max_digits:13',
            'role' => "required|in:$master_user_role",
            'bagian' => Rule::requiredIf($request->role == 'op' || $request->role == 'usr'),
            'instansi' => 'required_if:role,tmu',
            'kepentingan' => 'required_if:role,tmu',
            'level' => "required|in:$master_user_level",
            'jenis_file' => "array",

        ], [
            'nik.required' => 'NIK Harus Diisi',
            'nik.unique' => 'NIK Sudah Terdaftar',
            'nik.numeric' => 'NIK Harus Berupa Angka',
            'nama.required' => 'Nama Harus Diisi',
            'nama.min' => 'Nama Harus Diisi Minimal :min Huruf',
            'jabatan.required' => 'Jabatan Harus Diisi',
            'nohp.required' => 'No HP Harus Diisi',
            'nohp.numeric' => 'No HP Harus Berupa Angka',
            'nohp.min_digits' => 'No HP Harus Berjumlah :min Angka, ',
            'nohp.max_digits' => 'No HP Harus Berjumlah :max Angka, ',
            'bagian.required' => 'Bagian Harus Diisi',
            'level.required' => 'Level Harus Diisi',
            'level.in' => 'Level Harus Dalam Jangka :values',
        ]);

        $user = User::findOrFail($request->id);
        $user->update([
            // 'nik' => $request->nik,
            'name' => $request->nama,
            // 'password' => Hash::make($request->nik),
            'user_level' => $request->level,
            'jenis_file' => join(',', $request->jenis_file),
            'jenis_aksi' => join(',', $request->jenis_aksi),
            'active_from' => $request->active_from,
            'active_to' => $request->active_to,
            'active_status' => 1,
            'role' => $request->role
        ]);


        // dd($user->toArray());
        if ($user->role == 'tmu') {
            UserTamuDetail::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'no_hp' => $request->nohp,
                    'instansi' => $request->instansi,
                    'jabatan' => $request->jabatan,
                    'kepentingan' => $request->kepentingan
                ]
            );
        } elseif (in_array($user->role, ['op', 'usr'])) {
            UserBiasaDetail::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'no_hp' => $request->nohp,
                    'bagian' => $request->bagian,
                    'jabatan' => $request->jabatan
                ]
            );
        }
        return back()->with(['success' => true, 'message' => 'Berhasil Mengedit User']);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $user = User::findOrFail($request->id);
        $user->update([
            'password' => Hash::make($user->nik)
        ]);
        return back()->with(['success' => true, 'message' => 'Berhasil Mereset Password']);
    }

    public function active_user(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $user = User::findOrFail($request->id);
        $status = '';
        if ($user->active_status == 1) {
            $active_status = 0;
            $status = 'Nonaktifkan';
        } else {
            $active_status = 1;
            $status = 'Aktifkan';
        }
        $user->update([
            'active_status' => $active_status,
        ]);

        return back()->with(['success' => true, 'message' => "Berhasil $status User"]);
    }

    public function getDataUser(Request $request)
    {
        if ($request->ajax()) {
            $master_jenis_aksi = MasterJenisAksi::lazy();
            $data = User::with('level', 'uRole', 'uBagian', 'uBiasaDetail', 'uTamuDetail')
                        // ->leftJoin('master_jenis_aksi')
                        ->orderBy('created_at', 'desc')
                        ->lazy()


                        // ->get()
                        ->map(function($item) use ($master_jenis_aksi){
                            $jenis_aksi = $master_jenis_aksi->whereIn('id', explode(',', $item->jenis_aksi));
                            $item->aksi = $jenis_aksi->pluck('nama');
                            return $item;
                        });
            // dd($data->toArray());
            return DataTables::of($data->collect())->addIndexColumn()->make(true);
        }
    }

    public function getDataHistoryDokumen(Request $request){
        if($request->ajax()){
            $data = DokumenHistory::where('user_id', $request->id)->with('dokumen', 'aksi')->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }
}
