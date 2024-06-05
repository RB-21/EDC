<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\DokumenHistory;
use App\Models\MasterBagian;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpSigepFPDF;
use Yajra\DataTables\DataTables;
use Yasapurnama\DocumentWatermark\WatermarkFactory;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua jenis file dari table master_jenis_file dimana kodenya merupakan jenis file yang diizinkan untuk diakses yang di dapat dari tabel user
        $master_jenis_file = MasterJenisFile::whereIn('kode', explode(',', auth()->user()->jenis_file))->lazy();
        // Ambil semua data jenis aksi sesuai izin yang diberikan kepada user
        $master_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->lazy();
        $count_per_jenis_file_category = $master_jenis_file->map(function ($item) use ($master_jenis_aksi) {
            $item->count = Dokumen::where('jenis_file_kode', $item->kode)->count();
            $item->jenis_aksi = $master_jenis_aksi;
            if ($item->has_sub) {
                $item->bagian = MasterBagian::where('tipe', 'kandir')->first();
            }
            return $item;
        });
        return view('user.dashboard.index', compact('count_per_jenis_file_category', 'master_jenis_aksi'));
    }

    public function getDataDokumenByJenis(Request $request)
    {
        if ($request->ajax()) {
            $data = Dokumen::where('jenis_file_kode', $request->jenis_file)->orderBy('created_at', 'desc')->limit(5)->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
        return abort(500, 'Salah Method');
    }

    public function downloadDokumen(Request $request){
        // Cari Dokumen Dengan ID Tertentu
        $dokumen = Dokumen::findOrFail($request->id);

        // Periksa level akses user dengan level akses dokumen
        if(!in_array(auth()->user()->user_level, explode(',', $dokumen->level)) ){
            return view('401', [
                'dokumen_level' => $dokumen->level
            ]);
        }

        // Buat History Aksi User
        DokumenHistory::create([
            'dokumen_id' => $dokumen->id,
            'user_id' => auth()->user()->id,
            'jenis_aksi_id' => MasterJenisAksi::where('nama', 'Download')->first()->id
        ]);

        // Persiapkan objek untuk mengolah pdf
        $pdf = new PhpSigepFPDF();
        $pdf->addFont('impact', '', 'impact.php');

        // Path PDF
        $path = public_path('storage/'.$dokumen->dokumen);

        // Folder Tujuan PDF akan disimpan
        $outputPath = base_path('storage/app/public/watermark/watermark-'. base64_encode(auth()->user()->nik).'.pdf');

        // Coba set sumber pdf
        // Jika ada error jalankan catch
        try {
            $pdf->setSourceFile($path);
        } catch (\Throwable $th) {
            // Eksekusi command pdftk yang sudah diinstal di komputer server untuk uncompress file pdf
            exec('pdftk '. $path .' output ' . $outputPath . ' uncompress');
            // Ganti source file dengan pdf yang sudah di uncompress
            $pdf->setSourceFile($outputPath);
        }

        // Persiapkan teks watermark
        $watermarkNik = auth()->user()->nik;
        $watermarkNama = auth()->user()->name;
        $watermarkTanggal = Carbon::now()->toDateTimeString().' WIB';

        // Lakukan perulangan sesuai jumlah halaman pdf untuk menyusun ulang halaman pdf
        for($i = 1; $i <= $pdf->currentParser->getPageCount(); $i++){
            // // Tambahkan halaman pdf
            // $pdf->AddPage();
            // // import halaman file pdf
            // $tplIdx = $pdf->importPage($i);
            // $pdf->useTemplate($tplIdx, 0,0);

            $tplIdx = $pdf->importPage($i);
            $pageSize = $pdf->getTemplateSize($tplIdx);
            $pdf->AddPage($pageSize['h'] > $pageSize['w'] ? 'P' : 'L', array($pageSize['w'],$pageSize['h']));
            $pdf->useTemplate($tplIdx, 0,0);

            // Atur warna dan font watermark yang akan disisipkan
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetFont('impact', '',30);
            // $pdf->SetFont('Times', 'B', 30);

            // Tambahkan opacity pada watermark
            $pdf->SetAlpha(0.5);

            // Tambahkan gambar watermark beserta text watermark
            $pdf->Image(asset('img/salinan_stamp.png'),  $pdf->w - 60, 10, 50, 13);
            $pdf->RotatedText(($pdf->w / 2) - 20, ($pdf->h /2) - 20, $watermarkNik, 45);
            $pdf->RotatedText(($pdf->w / 2) - 20, ($pdf->h /2) - 0, $watermarkNama, 45);
            $pdf->RotatedText(($pdf->w / 2) - 20, ($pdf->h /2) + 20, $watermarkTanggal, 45);

            $pdf->SetXY(25, 25);
        }

        // Simpan watermark ke folder output
        $pdf->output($outputPath, 'F');

        $nama_dokumen = str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", $dokumen->judul);

        // kemudian download file
        return response()->download($outputPath,$nama_dokumen.'.pdf');
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
