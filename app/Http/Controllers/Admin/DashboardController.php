<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\MasterBagian;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index(){
        $master_jenis_file = MasterJenisFile::lazy();
        $master_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->lazy();
        $count_per_jenis_file_category = $master_jenis_file->map(function($item) use ($master_jenis_aksi){
            $item->count = Dokumen::where('jenis_file_kode', $item->kode)->count();
            $item->jenis_aksi = $master_jenis_aksi;
            if($item->has_sub){
                $item->bagian = MasterBagian::where('tipe', 'kandir')->first();
            }
            return $item;
        });
        return view('admin.dashboard.index', compact('count_per_jenis_file_category', 'master_jenis_aksi'));
    }

    public function getDataDokumenByJenis(Request $request){
        if($request->ajax()){
            if($request->ajax()){
                $data = Dokumen::where('jenis_file_kode', $request->jenis_file)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        }
        return abort(500, 'Salah Method');
    }

    public function downloadDokumen(Request $request)
    {
        $dokumen = Dokumen::findOrFail($request->id);

        if (!in_array(auth()->user()->user_level, explode(',', $dokumen->level))) {
            return view('401', [
                'dokumen_level' => $dokumen->level
            ]);
        }
        return response()->download(storage_path('app/public/'.$dokumen->dokumen));
    }
}
