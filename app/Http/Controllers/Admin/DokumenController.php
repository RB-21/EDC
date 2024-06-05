<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\DokumenHistoryPerubahan;
use App\Models\MasterBagian;
use App\Models\MasterJenisFile;
use App\Models\MasterStatusDokumen;
use App\Models\MasterStatusPerubahan;
use App\Models\MasterUserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Yasapurnama\DocumentWatermark\WatermarkFactory;

class DokumenController extends Controller
{
    public function index()
    {
        $master_jenis_file = MasterJenisFile::all();
        $master_bagian = MasterBagian::where('tipe', 'kandir')->orWhere('tipe', 'BOM')->get();
        $master_user_level = MasterUserLevel::all();
        $master_status_perubahan = MasterStatusPerubahan::all();
        return view('admin.dokumen.index', compact('master_jenis_file', 'master_bagian', 'master_user_level', 'master_status_perubahan'));
    }

    public function index_by_jenis($jenis_file, $bagian = null)
    {
        $master_jenis_file = MasterJenisFile::all();
        $master_bagian = MasterBagian::where('tipe', 'kandir')->orWhere('tipe', 'BOM')->get();
        $master_user_level = MasterUserLevel::all();
        $master_status_perubahan = MasterStatusPerubahan::all();
        $jenis_file = MasterJenisFile::where('kode', $jenis_file)->firstOrFail();
        $bagian = MasterBagian::where('kode_bagian', $bagian)->first();
        return view('admin.dokumen.index_by_jenis', compact('jenis_file', 'bagian', 'master_jenis_file', 'master_bagian', 'master_user_level', 'master_status_perubahan'));
    }

    public function simpan(Request $request)
    {
        $master_jenis_file = MasterJenisFile::pluck('kode')->implode(',');
        $master_bagian = MasterBagian::pluck('kode_bagian')->implode(',');
        $master_status_perubahan = MasterStatusPerubahan::pluck('id')->implode(',');
        $master_status_perubahan_revisi_id = MasterStatusPerubahan::whereIn('nama', ['dicabut', 'revisi'])->pluck('id')->toArray();
        $request->validate([
            'jenis_file' => "required|in:$master_jenis_file",
            'nomor' => 'required',
            'judul' => 'required',
            'tanggal' => 'required|date',
            'bagian' => "required|in:$master_bagian",
            'level' => 'required',
            'dokumen' => 'required|file|mimes:pdf',
            'status_dokumen' => "required|in:$master_status_perubahan",
            'dokumen_berubah' => Rule::requiredIf(fn () => in_array($request->status_dokumen, ['revisi', 'dicabut']))
        ]);
        // dd($request->all());
        Storage::putFile('public', $request->dokumen);
        // dd($request->all(), $request->dokumen->hashName());
        if (in_array($request->status_dokumen, $master_status_perubahan_revisi_id)) {
            $dokumen_lama = Dokumen::findOrFail($request->dokumen_berubah);
            $dokumen = Dokumen::create([
                'jenis_file_kode' => $request->jenis_file,
                'nomor' => trim($request->nomor, ' '),
                'judul' => $request->judul,
                'tanggal' => $request->tanggal,
                'bagian' => $request->bagian,
                'level' => join(',', $request->level),
                'status_dokumen_id' => MasterStatusDokumen::where('nama', 'berlaku')->first()->id,
                'status_perubahan_id' => MasterStatusPerubahan::where('nama', 'baru')->first()->id,
                'dokumen_base_id' => $dokumen_lama->dokumen_base_id,
                'dokumen_prev_id' => $dokumen_lama->id,
                'dokumen_next_id' => null,
                'dokumen' => $request->dokumen->hashName(),
                'uploaded_by' => auth()->user()->id,
            ]);
            $dokumen_lama->update([
                'status_dokumen_id' => MasterStatusDokumen::where('nama', 'kadaluarsa')->first()->id,
                'status_perubahan_id' => $request->status_dokumen,
                'dokumen_next_id' => $dokumen->id
            ]);
            DokumenHistoryPerubahan::create([
                'dokumen_awal_id' => $dokumen_lama->dokumen_base_id,
                'dokumen_lama_id' => $dokumen_lama->id,
                'dokumen_baru_id' => $dokumen->id,
                'status_perubahan_id' => $request->status_perubahan
            ]);
        } else {
            // cek nomor dokumen
            $check_no_dokumen = Dokumen::where('nomor', $request->nomor)->first();
            // kalau belum ada dokumen dengan nomor tersebut
            if (empty($check_no_dokumen)) {
                // Buat dokumen
                $dokumen = Dokumen::create([
                    'jenis_file_kode' => $request->jenis_file,
                    'nomor' => trim($request->nomor, ' '),
                    'judul' => $request->judul,
                    'tanggal' => $request->tanggal,
                    'bagian' => $request->bagian,
                    'level' => join(',', $request->level),
                    'status_dokumen_id' => MasterStatusDokumen::where('nama', 'berlaku')->first()->id,
                    'status_perubahan_id' => MasterStatusPerubahan::where('nama', 'baru')->first()->id,
                    'dokumen' => $request->dokumen->hashName(),
                    'uploaded_by' => auth()->user()->id,
                ]);
                $dokumen->update([
                    'dokumen_base_id' => $dokumen->id,
                    'dokumen_prev_id' => null,
                    'dokumen_next_id' => null,
                ]);
                DokumenHistoryPerubahan::create([
                    'dokumen_awal_id' => $dokumen->id,
                    'dokumen_lama_id' => $dokumen->id,
                    'dokumen_baru_id' => $dokumen->id,
                    'status_perubahan_id' => MasterStatusPerubahan::where('nama', 'baru')->first()->id
                ]);
            } else {
                return back()->with(['success' => false, 'message' => 'Nomor Dokumen Sudah Digunakan']);
            }
        }

        return back()->with(['success' => true, 'message' => 'Berhasil Menambahkan Dokumen']);
    }
    public function edit(Request $request)
    {
        // dd($request->all(), getNextDocument(1409, Date('now')));
        $master_jenis_file = MasterJenisFile::pluck('kode')->implode(',');
        $master_bagian = MasterBagian::pluck('kode_bagian')->implode(',');
        $master_status_perubahan = MasterStatusPerubahan::pluck('id')->implode(',');
        $master_status_perubahan_revisi_id = MasterStatusPerubahan::whereIn('nama', ['dicabut', 'revisi'])->pluck('id')->toArray();

        $request->validate([
            'jenis_file' => "required|in:$master_jenis_file",
            'nomor' => 'required',
            'judul' => 'required',
            'tanggal' => 'required|date',
            'bagian' => "required|in:$master_bagian",
            'level' => 'required',
            'dokumen' => 'nullable|file|mimes:pdf',
            'status_dokumen' => "required|in:$master_status_perubahan",
            'dokumen_berubah' => Rule::requiredIf(fn () => in_array($request->status_dokumen, ['revisi', 'dicabut']))
        ]);

        // Check apakah dokumen dengan nomor ini sudah ada
        $dokumen_check1 = Dokumen::where('nomor', $request->nomor)->first();
        $dokumen_check2 = Dokumen::findOrFail($request->id);

        // Kalau ada
        if (!empty($dokumen_check1)) {
            // dan kedua dokumen memiliki id yang berbeda
            if (!($dokumen_check1->id === $dokumen_check2->id)) {
                // Maka kembali dengan pesan
                return back()->with(['success' => false, 'message' => 'Nomor Surat Sudah Terdaftar']);
            }
        }

        if (!empty($request->dokumen)) {
            Storage::putFile('public', $request->dokumen);
            // dd($request->all(), $request->dokumen->hashName());
            $dokumen_check2->update([
                'jenis_file_kode' => $request->jenis_file,
                'nomor' => trim($request->nomor, ' '),
                'judul' => $request->judul,
                'tanggal' => $request->tanggal,
                'bagian' => $request->bagian,
                'level' => join(',', $request->level),
                'dokumen' => $request->dokumen->hashName(),
                // 'uploaded_by' => auth()->user()->id,
            ]);
        } else {
            $dokumen_check2->update([
                'jenis_file_kode' => $request->jenis_file,
                'nomor' => trim($request->nomor, ' '),
                'judul' => $request->judul,
                'tanggal' => $request->tanggal,
                'bagian' => $request->bagian,
                'level' => join(',', $request->level),
                // 'dokumen' => $request->dokumen->hashName(),
                // 'uploaded_by' => auth()->user()->id,
            ]);
        }

        if (in_array($request->status_dokumen, $master_status_perubahan_revisi_id)){
            $dokumen_lama = Dokumen::findOrFail($request->dokumen_berubah);


            // if(getNextDocument($dokumen_lama->dokumenB_base_id, $dokumen_check2->tanggal)){
            //     dd('masuk');
            // }

            // Start V1 ##
            $dokumen_check2->update([
                'status_dokumen_id' => getNextDocument($dokumen_lama->dokumen_base_id, $dokumen_check2->tanggal) ? MasterStatusDokumen::where('nama', 'kadaluarsa')->first()->id : MasterStatusDokumen::where('nama', 'berlaku')->first()->id,
                'status_perubahan_id' => MasterStatusPerubahan::where('nama', 'baru')->first()->id,
                'dokumen_base_id' => $dokumen_lama->dokumen_base_id,
                'dokumen_prev_id' => $dokumen_lama->id,
                'dokumen_next_id' => getNextDocument($dokumen_lama->dokumen_base_id, $dokumen_check2->tanggal),
            ]);

            $dokumen_lama->update([
                'status_dokumen_id' => MasterStatusDokumen::where('nama', 'kadaluarsa')->first()->id,
                'status_perubahan_id' => $request->status_dokumen,
                'dokumen_next_id' => $dokumen_check2->id
            ]);
            DokumenHistoryPerubahan::create([
                'dokumen_awal_id' => $dokumen_lama->dokumen_base_id,
                'dokumen_lama_id' => $dokumen_lama->id,
                'dokumen_baru_id' => $dokumen_check2->id,
                'status_perubahan_id' => $request->status_perubahan
            ]);
            // End V1 ##
        }
        // else {
        //     $dokumen_check2->update([
        //         'status_dokumen_id' => MasterStatusDokumen::where('nama', 'berlaku')->first()->id,
        //         'status_perubahan_id' => MasterStatusPerubahan::where('nama', 'baru')->first()->id,
        //         'dokumen_base_id' => $dokumen_lama->dokumen_base_id,
        //         'dokumen_prev_id' => $dokumen_lama->id,
        //         'dokumen_next_id' => getNextDocument($dokumen_lama->dokumen_base_id, $dokumen_check2->tanggal),
        //     ]);
        // }

        return back()->with(['success' => true, 'message' => 'Berhasil Mengupdate Dokumen']);
    }

    public function check_nomor(Request $request)
    {
        $dokumen = Dokumen::where('nomor', $request->nomor)->first();
        if (!empty($dokumen)) {
            return response()->json([
                'status' => 1,
                'data' => $dokumen
            ]);
        }
        return response()->json([
            'status' => 0,
            'data' => null
        ]);
    }

    public function getDokumenPerubahan(Request $request)
    {
        $list_dokumen = Dokumen::where('nomor', 'like', "%$request->input%")->orWhere('judul', 'like', "%$request->input%")->get();
        return response()->json([
            'status' => 1,
            'data' => $list_dokumen
        ]);
    }

    public function tampil(Request $request)
    {
        $dokumen = Dokumen::findOrFail($request->id);
        $path = asset('storage/' . $dokumen->dokumen);
        $judul = $dokumen->nomor . '-' . $dokumen->judul;
        // return response()->file(base_path('storage/app/public/'.$dokumen->dokumen));
        return view('pdfjs.template', compact('path', 'judul', 'dokumen'));
    }

    public function loadDokumen($id){
        $dokumen = Dokumen::findOrFail($id);
        $path = base_path('storage/app/public/' . $dokumen->dokumen);
        return response()->file($path, ['Content-Type' => 'application/tampil']);
    }

    public function preview()
    {
        $path = public_path('storage/Sg18d3t1GrTkVKh1BJT9vvkdTxZHYeVcogMLbDmb.pdf');

        // $pdfWatermark = WatermarkFactory::load($path)->subDirectory('watermark')
        // ->setText('Last update on ' . date('m/d/Y'))
        // ->sectionHeader()
        // ->alignLeft(0, 50)
        // ->fontSize(30)
        // ->fontColor('ff0000')
        // ->generate();

        // $pdfWatermark = WatermarkFactory::load($pdfWatermark)->subDirectory('watermark')
        // ->setText('Last update on ' . date('m/d/Y'))
        // ->sectionFooter()
        // ->alignCenter()
        // ->angle(45)
        // ->fontSize(30)
        // ->fontColor('ff0000')
        // ->generate();

        // $pdfWatermark = WatermarkFactory::load($pdfWatermark)->subDirectory('watermark')
        // ->setText('Last update on ' . date('m/d/Y'))
        // ->sectionFooter()
        // ->alignRight(0,50)
        // ->fontSize(30)
        // ->fontColor('ff0000')
        // ->generate();
        // dd($pdfWatermark);
        // return response()->file($path, ['Content-Type' => 'application/pdf']);
        // return response()->file($pdfWatermark, ['Content-Type' => 'application/pdf']);
        // return response()->file($path, ['Content-Type' => 'application/pdf']);
    }

    public function getDataDokumen(Request $request)
    {
        if ($request->ajax()) {
            $data = Dokumen::with('dJenisFile', 'dBagian', 'dStatus', 'dPrev', 'dNext')->orderBy('created_at', 'desc')->get();
            // dd($data);
            // get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function getDataDokumenByJenis(Request $request)
    {
        if ($request->ajax()) {
            $data = Dokumen::where('jenis_file_kode', $request->jenis_file);
            if (!empty($request->bagian)) {
                $data = $data->where('bagian', $request->bagian);
            }
            $data = $data->with('dPrev', 'dNext','dJenisFile', 'dBagian', 'dStatus')
                ->orderBy('tanggal', 'desc')
                // ->limit(5)
                ->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function getDokumenHistory(Request $request)
    {
        if ($request->ajax()) {
            $dokumen = Dokumen::where('id', $request->id)->with(['dHistory' => function ($query) {
                $query->with('user', 'aksi')->orderBy('created_at', 'desc');
            }])->first();
            // $table_html = '';
            // foreach($dokumen->dHistory as $item){
            //     $user = User::findOrFail($item->user_id);
            //     $table_html .= '<tr>';
            //     $table_html .= "<td>$user->nik</td>";
            //     $table_html .= "<td>$user->name</td>";
            //     $table_html .= "<td>$item->created_at WIB</td>";
            //     $table_html .= "</tr>\n";
            // }
            return DataTables::of($dokumen->dHistory)->addIndexColumn()->make(true);
            // return response()->json([
            //     'tableBody' => $table_html
            // ]);
        }
    }

    public function getPerubahanDokumenHistory(Request $request){
        $dokumen = Dokumen::findOrFail($request->id);
        $list_dokumen = Dokumen::where('dokumen_base_id', $dokumen->dokumen_base_id)
                        ->with( 'dStatus', 'dStatusPerubahan', 'dPrev', 'dNext')
                        ->orderBy('tanggal', 'desc')
                        ->get();
        return DataTables::of($list_dokumen)->addIndexColumn()->make(true);
    }
}
