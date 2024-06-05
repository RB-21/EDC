<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use FPDISigep;
use Illuminate\Http\Request;
use PhpSigepFPDF;

// use TCPDI;


class DevelopmentController extends Controller
{
    public function preview(Request $request){
        dd($request->all(), $request->session(), csrf_token());
        return response()->file(base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik) .'.pdf'),['Content-Type' => 'text/html']);
    }

    public function tes1(){
        $tes = new PhpSigepFPDF();
        $path = base_path('storage/app/public/0dDxIpHKLKJgPgK6tD8QYyJyMXiIBPyVhbUxCWIQ.pdf');
        // dd($path);
        $tes->setSourceFile($path);
        // dd($tes, $path, $tes->currentParser->getPageCount());
        for($i = 1; $i <= $tes->currentParser->getPageCount(); $i++){
            $tes->AddPage();
            $tplIdx = $tes->importPage($i);
            $tes->useTemplate($tplIdx, 0,0);
            $tes->SetFont('Times', 'B', 20);
            $tes->SetTextColor(255, 0, 0);
            $watermarkText = "CONFIDENTAL 1";
            $watermarkText2 = "CONFIDENTAL 2";
            $tes->SetAlpha(0.5);
            $tes->Image(asset('img/salinan_stamp.png'),  $tes->w - 60, 30, 50, 15);
            $tes->RotatedText($tes->w / 2, $tes->h /2, 'Text Rotated', 45);
            // $this->addWatermark(105, 220, $watermarkText, 45, $tes);
            // $this->addWatermark(105, 240, $watermarkText2, 45, $tes);
            $tes->SetXY(25, 25);
        }
        $path = explode('public', public_path('storage/watermark/watermark.pdf'))[1];
        $judul = 'tes1';
        // dd(storage_path('watermark'), base_path('storage/app/public/watermark'),$tes);
        $tes->output(base_path('storage/app/public/watermark/watermark.pdf'), 'F');
        return view('pdfjs.template', compact('path', 'judul'));
    }

    // function addWatermark($x, $y, $watermarkText, $angle, $pdf)
    // {
    //     $angle = $angle * M_PI / 180;
    //     $c = cos($angle);
    //     $s = sin($angle);
    //     $cx = $x * 1;
    //     $cy = (300 - $y) * 1;
    //     $pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
    //     $pdf->Text($x, $y, $watermarkText);
    //     $pdf->_out('Q');
    // }

    public function tes2(){
        $pdf = new TCPDI();
        dd($pdf);
    }

    public function comma(){
        $user = \App\Models\User::findOrFail(34)->with('getAksi');
        dd($user);
        return 'berhasil';
    }

    public function fill_dhp(){
        $data = \App\Models\Dokumen::lazy()->each(function($item){
            $item->update([
                'dokumen_link_id' => $item->id,
                'status_perubahan_id' => 1
            ]);
            // \App\Models\DokumenHistoryPerubahan::create([
            //     'dokumen_awal_id' => $item->id,
            //     'dokumen_lama_id' => $item->id,
            //     'dokumen_baru_id' => $item->id,
            //     'status_perubahan_id' => 1
            // ]);
        });
        dd($data->toArray());
    }

    public function normalize(){
        $data = \App\Models\Dokumen::cursor();
        dd($data->groupBy('dokumen_base_id')->each(function($item){
            $item->toArray();
        })->toArray());
    }

    public function pdf17(){
        $dokumen = \App\Models\Dokumen::findOrFail(34);

        $judul = $dokumen->judul;
        $path = public_path('storage/'.$dokumen->dokumen);

        $pdf = new PhpSigepFPDF();

        try {
            $pdf->setSourceFile($path);
        } catch (\Throwable $th) {
            $watermarkName = base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik) .'.pdf');
            $pdf->setSourceFile($watermarkName);
            // dd(exec('pdftk '. $path . ' output '. $watermarkName . ' uncompress'), $watermarkName);
            // throw $th;
        }

        $watermarkNik = auth()->user()->nik;
        $watermarkNama = auth()->user()->name;
        $watermarkTanggal = Carbon::now()->toDateTimeString().' WIB';
        // dd($pdf->currentParser->filename);
        // dd($pdf);
        for($i = 1; $i <= $pdf->currentParser->getPageCount(); $i++){
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($i);
            $pdf->useTemplate($tplIdx, 0,0);
            $pdf->SetTextColor(255, 0, 0);

            $pdf->SetAlpha(0.5);
            $pdf->SetFont('Times', 'B', 30);
            $pdf->Image(asset('img/salinan_stamp.png'),  $pdf->w - 60, 10, 50, 13);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) - 30, $watermarkNik, 45);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) - 10, $watermarkNama, 45);
            $pdf->RotatedText(($pdf->w / 2) - 30, ($pdf->h /2) + 10, $watermarkTanggal, 45);

            // $pdf->SetFont('Times', 'B', 20);
            // $pdf->RotatedText(10, $pdf->h - 30, $watermarkInstansi, 90);
            // $pdf->RotatedText(10 + 8, $pdf->h - 30, $watermarkKepentingan, 90);
            // $this->addWatermark(105, 220, $watermarkText, 45, $pdf);
            // $this->addWatermark(105, 240, $watermarkText2, 45, $pdf);
            $pdf->SetXY(25, 25);
        }
        // dd($watermarkName);
        // dd($pdf,$watermarkNik, $watermarkNama, $watermarkTanggal);
        $pdf->output($pdf->currentFilename, 'F');
        $filename = explode('public', $pdf->currentFilename)[1];
        // dd($filename);
        $path = '/storage'.$filename;

        return view('pdfjs.template2', compact('judul', 'path'));

        $pdf = new PhpSigepFPDF();
        // $path = public_path('storage/tespdf.pdf');
        // $path = public_path('storage/tespdf.unc.pdf');
        $path = public_path('storage/H76wpWoPSBhLkNBn8btrSf0F86IeJe4zpmaNzPGm.pdf');
        try {
            $pdf->setSourceFile($path);
        } catch (\Throwable $th) {
            $path = public_path('storage/H76wpWoPSBhLkNBn8btrSf0F86IeJe4zpmaNzPGm.unc.pdf');
            $pdf->setSourceFile($path);
            $path = explode('public', $path)[1];
            // dd('tes', $th, $pdf);
        }
        $judul = 'judul';
        // dd('lanjut', $path);
        // $path = explode('public', base_path('storage/app/public/TES PDF 2.pdf'))[1];
        // return response()->file(base_path('storage/app/public/TES PDF 2.pdf'));
        return view('pdfjs.template2', compact('judul', 'path'));
    }

    public function check_file($url){
        return response()->file(base_path('storage/app/public/watermark/'.$url, ['Content-Type' => 'text/html']));
    }
}
