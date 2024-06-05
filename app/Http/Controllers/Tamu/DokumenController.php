<?php

namespace App\Http\Controllers\Tamu;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\DokumenHistory;
use App\Models\MasterBagian;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use App\Models\MasterUserLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpSigepFPDF;
use Yajra\DataTables\DataTables;
use Yasapurnama\DocumentWatermark\WatermarkFactory;

class DokumenController extends Controller
{
    public function index(){
        $master_jenis_file = MasterJenisFile::all();
        $master_bagian = MasterBagian::where('tipe', 'kandir')->get();
        $master_user_level = MasterUserLevel::all();
        $master_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->get();
        return view('tamu.dokumen.index', compact('master_jenis_file','master_bagian', 'master_user_level', 'master_jenis_aksi'));
    }

    public function index_by_jenis($jenis_file, $bagian = null){
        if(!in_array($jenis_file, explode(',', auth()->user()->jenis_file))){
            return back();
        }
        $jenis_file = MasterJenisFile::where('kode', $jenis_file)->firstOrFail();
        $bagian = MasterBagian::where('kode_bagian', $bagian)->first();
        $jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->get();
        return view('tamu.dokumen.index_by_jenis', compact('jenis_file','bagian', 'jenis_aksi'));
    }

    // public function simpan(Request $request){
    //     $master_jenis_file = MasterJenisFile::pluck('kode')->implode(',');
    //     $master_bagian = MasterBagian::pluck('kode_bagian')->implode(',');
    //     $request->validate([
    //         'jenis_file' => "required|in:$master_jenis_file",
    //         'nomor' => 'required',
    //         'judul' => 'required',
    //         'tanggal' => 'required|date',
    //         'bagian' => "required|in:$master_bagian",
    //         'level' => 'required',
    //         'dokumen' => 'required|file|mimes:pdf'
    //     ]);

    //     Storage::putFile('public', $request->dokumen);
    //     // dd($request->all(), $request->dokumen->hashName());
    //     Dokumen::create([
    //         'jenis_file_kode' => $request->jenis_file,
    //         'nomor' => $request->nomor,
    //         'judul' => $request->judul,
    //         'tanggal' => $request->tanggal,
    //         'bagian' => $request->bagian,
    //         'level' => join(',',$request->level),
    //         'dokumen' => $request->dokumen->hashName()
    //     ]);

    //     return back()->with(['success' => true, 'message' => 'Berhasil Menambahkan Dokumen']);
    // }

    public function tampil(Request $request){
        $dokumen = Dokumen::findOrFail($request->id);

        if(!in_array(auth()->user()->user_level, explode(',', $dokumen->level)) ){
            return view('401', [
                'dokumen_level' => $dokumen->level
            ]);
        }

        DokumenHistory::create([
            'dokumen_id' => $dokumen->id,
            'user_id' => auth()->user()->id,
            'jenis_aksi_id' => MasterJenisAksi::where('nama', 'Lihat')->first()->id
        ]);

        $pdf = new PhpSigepFPDF();
        $pdf->addFont('impact', '', 'impact.php');
        $judul = $dokumen->nomor.'-'.$dokumen->judul;
        $path = public_path('storage/'.$dokumen->dokumen);
        $outputPath = base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik).'.pdf');
        $is_lanscape = false;

        try {
            $pdf->setSourceFile($path);
        } catch (\Throwable $th) {
            exec('pdftk '. $path .' output ' . $outputPath . ' uncompress');
            $pdf->setSourceFile($outputPath);
        }
        // dd($pdf, $path, $pdf->currentParser->getPageCount());
        $watermarkNik = auth()->user()->nik;
        $watermarkNama = auth()->user()->name;
        $watermarkTanggal = Carbon::now()->toDateTimeString().' WIB';
        $watermarkInstansi = auth()->user()->uTamuDetail->instansi;
        $watermarkKepentingan = auth()->user()->uTamuDetail->kepentingan;
        for($i = 1; $i <= $pdf->currentParser->getPageCount(); $i++){
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($i);
            $pdf->useTemplate($tplIdx, 0,0);
            $pdf->SetTextColor(255, 0, 0);

            $pdf->SetAlpha(0.5);
            $pdf->SetFont('impact', '',30);
            // $pdf->SetFont('Times', 'B', 30);
            $pdf->Image(asset('img/salinan_stamp.png'),  $pdf->w - 60, 10, 50, 13);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) - 30, $watermarkNik, 45);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) - 10, $watermarkNama, 45);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) + 10, $watermarkTanggal, 45);

            $pdf->SetFont('Times', 'B', 20);
            $pdf->RotatedText(10, $pdf->h - 30, $watermarkInstansi, 90);
            $pdf->RotatedText(10 + 8, $pdf->h - 30, $watermarkKepentingan, 90);
            // $this->addWatermark(105, 220, $watermarkText, 45, $pdf);
            // $this->addWatermark(105, 240, $watermarkText2, 45, $pdf);
            $pdf->SetXY(25, 25);
        }
        $pdf->output($outputPath, 'F');

        return view('tamu.pdfjs.template', compact('path', 'judul'));
    }

    public function tampil_v1(Request $request){
        $dokumen = Dokumen::findOrFail($request->id);

        if(!in_array(auth()->user()->user_level, explode(',', $dokumen->level)) ){
            return view('401', [
                'dokumen_level' => $dokumen->level
            ]);
        }

        DokumenHistory::create([
            'dokumen_id' => $dokumen->id,
            'user_id' => auth()->user()->id,
            'jenis_aksi_id' => MasterJenisAksi::where('nama', 'Lihat')->first()->id
        ]);

        $path = public_path('storage/'.$dokumen->dokumen);
        // dd($dokumen,$path);
        $pdfWatermark = WatermarkFactory::load($path)->subDirectory('watermark')
                        ->setImage(public_path('img/salinan_stamp.png'))
                        ->setImageScale(10)
                        ->sectionHeader()
                        ->alignRight(0, 20)
                        // ->fontSize(30)
                        // ->fontColor('ff0000')
                        ->opacity(0.5)
                        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)
        ->setText(auth()->user()->nik)
        ->alignCenter()
        ->alignLeft(230, 400)
        ->angle(45)
        ->fontSize(30)
        ->fontColor('f48181')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)
        ->setText(auth()->user()->name)
        ->alignCenter()
        ->alignLeft(230, 450)
        ->angle(45)
        ->fontSize(30)
        ->fontColor('f48181')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)
        ->setText(auth()->user()->uTamuDetail->instansi)
        ->alignCenter()
        ->alignLeft(40,95)
        ->sectionFooter()
        ->angle(90)
        ->fontSize(20)
        ->fontColor('f48181')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)
        ->setText(auth()->user()->uTamuDetail->kepentingan)
        ->alignCenter()
        ->sectionFooter()
        ->alignLeft(63, 95)
        ->angle(90)
        ->fontSize(20)
        ->fontColor('f48181')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)
                        ->setText(Carbon::now()->toDateTimeString().' WIB')
                        // ->sectionFooter()
                        ->alignCenter()
                        ->alignLeft(230, 500)
                        ->angle(45)
                        ->fontSize(30)
                        ->fontColor('f48181')
                        ->generate();


        // dd($request->all(), $dokumen, $path, $pdfWatermark, );
        $path = explode('public',$pdfWatermark)[1];
        $judul = $dokumen->nomor.'-'.$dokumen->judul;
        return view('pdfjs.template', compact('path', 'judul'));
    }

    public function preview(){
        $path = public_path('storage/Sg18d3t1GrTkVKh1BJT9vvkdTxZHYeVcogMLbDmb.pdf');

        $pdfWatermark = WatermarkFactory::load($path)->subDirectory('watermark')
        ->setText(Carbon::now())
        ->sectionHeader()
        ->alignLeft(0, 50)
        ->fontSize(30)
        ->fontColor('ff0000')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)->subDirectory('watermark')
        ->setText(auth()->user()->name)
        ->sectionFooter()
        ->alignCenter()
        ->angle(45)
        ->fontSize(30)
        ->fontColor('ff0000')
        ->generate();

        $pdfWatermark = WatermarkFactory::load($pdfWatermark)->subDirectory('watermark')
        ->setText(Carbon::now())
        ->sectionFooter()
        ->alignRight(0,50)
        ->fontSize(30)
        ->fontColor('ff0000')
        ->generate();
        // dd($pdfWatermark);
        // return response()->file($path, ['Content-Type' => 'application/pdf']);
        return response()->file($pdfWatermark, ['Content-Type' => 'application/pdf']);
    }

    public function loadDokumen(){
        return response()->file(base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik) .'.pdf'), ['Content-Type' => 'application/tampil']);
    }

    public function getDataDokumen(Request $request){
        if($request->ajax()){
            $data = Dokumen::whereIn('jenis_file_kode', explode(',', auth()->user()->jenis_file))->with('dJenisFile', 'dBagian','dStatus', 'dPrev', 'dNext')->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function getDataDokumenByJenis(Request $request){
        if($request->ajax()){
            $data = Dokumen::where('jenis_file_kode', $request->jenis_file);
            if(!empty($request->bagian)){
                $data = $data->where('bagian', $request->bagian);
            }
            $data = $data->with('dJenisFile', 'dBagian','dStatus', 'dPrev', 'dNext','dStatus', 'dPrev', 'dNext')
                    ->orderBy('created_at', 'desc')
                    // ->limit(5)
                    ->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function getPerubahanDokumenHistory(Request $request)
    {
        $dokumen = Dokumen::findOrFail($request->id);
        $list_dokumen = Dokumen::where('dokumen_base_id', $dokumen->dokumen_base_id)
            ->with('dStatus', 'dStatusPerubahan', 'dPrev', 'dNext')
            ->orderBy('tanggal', 'desc')
            ->get();
        return DataTables::of($list_dokumen)->addIndexColumn()->make(true);
    }
}
