<?php

namespace App\Http\Controllers\Tamu;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\DokumenHistory;
use App\Models\MasterBagian;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpSigepFPDF;
use Yajra\DataTables\DataTables;
use Yasapurnama\DocumentWatermark\WatermarkFactory;

class DashboardController extends Controller
{
    public function index(){
        $master_jenis_file = MasterJenisFile::whereIn('kode', explode(',',auth()->user()->jenis_file))->lazy();
        $master_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->lazy();
        $count_per_jenis_file_category = $master_jenis_file->map(function($item) use ($master_jenis_aksi){
            $item->count = Dokumen::where('jenis_file_kode', $item->kode)->count();
            $item->jenis_aksi = $master_jenis_aksi;
            if($item->has_sub){
                $item->bagian = MasterBagian::where('tipe', 'kandir')->first();
            }
            return $item;
        });
        return view('tamu.dashboard.index', compact('count_per_jenis_file_category', 'master_jenis_aksi'));
    }

    public function getDataDokumenByJenis(Request $request){
        if($request->ajax()){
            if($request->ajax()){
                $data = Dokumen::where('jenis_file_kode', $request->jenis_file)->orderBy('created_at', 'desc')->limit(5)->get();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        }
        return abort(500, 'Salah Method');
    }

    public function downloadDokumen(Request $request){
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
        // dd(storage_path('watermark'), base_path('storage/app/public/watermark'),$pdf);
        // $path_watermark = base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik) .'.pdf');
        $pdf->output($outputPath, 'F');

        // $path = explode('public', public_path('storage/watermark/watermark.pdf'))[1];
        // $judul = $dokumen->nomor.'-'.$dokumen->judul;
        // return view('pdfjs.template', compact('path', 'judul'));

        $nama_dokumen = str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", $dokumen->judul);
        return response()->download($outputPath, "$nama_dokumen.pdf");
    }

    public function downloadDokumenV1(Request $request)
    {
        $dokumen = Dokumen::findOrFail($request->id);

        if (!in_array(auth()->user()->user_level, explode(',', $dokumen->level))) {
            return view('401', [
                'dokumen_level' => $dokumen->level
            ]);
        }

        DokumenHistory::create([
            'dokumen_id' => $dokumen->id,
            'user_id' => auth()->user()->id,
            'jenis_aksi_id' => MasterJenisAksi::where('nama', 'Download')->first()->id
        ]);

        $path = public_path('storage/' . $dokumen->dokumen);
        // dd($dokumen,$path, );
        $pdfWatermark = WatermarkFactory::load($path)->subDirectory('watermark')
            ->setImage(public_path('img/salinan_stamp.png'))
            ->setImageScale(10)
            ->sectionHeader()
            ->alignRight(0, 20)
            // ->fontSize(30)
            // ->fontColor('ff0000')
            ->opacity(0.5)
            ->generate();
        // dd($pdfWatermark);
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
            ->setText(Carbon::now()->toDateTimeString() . ' WIB')
            // ->sectionFooter()
            ->alignCenter()
            ->alignLeft(230, 500)
            ->angle(45)
            ->fontSize(30)
            ->fontColor('f48181')
            ->generate();


        // dd($request->all(), $dokumen, $path, $pdfWatermark, );
        // dd($path);
        return response()->download($pdfWatermark);
    }

}
