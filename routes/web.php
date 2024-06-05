<?php

use Ajaxray\PHPWatermark\Watermark;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DokumenController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Operator\DokumenController as OperatorDokumenController;
use App\Http\Controllers\Operator\ProfileController as OperatorProfileController;
use App\Http\Controllers\Tamu\DashboardController as TamuDashboardController;
use App\Http\Controllers\Tamu\DokumenController as TamuDokumenController;
use App\Http\Controllers\Tamu\ProfileController as TamuProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DokumenController as UserDokumenController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('lupa-password', [AuthController::class, 'lupa_password'])->name('lupa_password');
Route::post('lupa-password', [AuthController::class, 'konfirmasi_lupa_password'])->name('konfirmasi_lupa_password');
Route::get('update_password', [AuthController::class, 'update_password'])->name('update_password');
Route::post('update_password', [AuthController::class, 'update_password_process'])->name('update_password_process');

Route::middleware('auth')->group(function () {
    Route::middleware('auth.role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::post('/', [DashboardController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
        Route::post('/downloadDokumen', [DashboardController::class, 'downloadDokumen'])->name('downloadDokumen');
        Route::get('loadDokumen/{id}', [DokumenController::class, 'loadDokumen'])->name('loadDokumen');

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('simpan', [UserController::class, 'simpan'])->name('simpan');
            Route::post('edit', [UserController::class, 'edit'])->name('edit');
            Route::post('reset_password', [UserController::class, 'reset_password'])->name('reset_password');
            Route::post('active_user', [UserController::class, 'active_user'])->name('active_user');
            Route::post('getDataUser', [UserController::class, 'getDataUser'])->name('getDataUser');
            Route::post('getDataHistoryDokumen', [UserController::class, 'getDataHistoryDokumen'])->name('getDataHistoryDokumen');
        });

        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/', [DokumenController::class, 'index'])->name('index');
            $master_bagian_kandir = \App\Models\MasterBagian::where('tipe', 'kandir');
            Route::get('{jenis_file}/{bagian?}', [DokumenController::class, 'index_by_jenis'])->whereIn('bagian', $master_bagian_kandir->pluck('kode_bagian')->toArray())->name('index_by_jenis');
            Route::post('simpan', [DokumenController::class, 'simpan'])->name('simpan');
            Route::post('edit', [DokumenController::class, 'edit'])->name('edit');
            Route::post('check-nomor', [DokumenController::class, 'check_nomor'])->name('check-nomor');
            Route::post('getDokumenPerubahan', [DokumenController::class, 'getDokumenPerubahan'])->name('getDokumenPerubahan');
            Route::post('tampil', [DokumenController::class, 'tampil'])->name('tampil');
            Route::get('preview', [DokumenController::class, 'preview'])->name('preview');
            Route::post('getDataDokumen', [DokumenController::class, 'getDataDokumen'])->name('getDataDokumen');
            Route::post('getDataDokumenByJenis', [DokumenController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
            Route::post('getDokumenHistory', [DokumenController::class, 'getDokumenHistory'])->name('getDokumenHistory');
            Route::post('getPerubahanDokumenHistory', [DokumenController::class, 'getPerubahanDokumenHistory'])->name('getPerubahanDokumenHistory');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('', [ProfileController::class, 'index'])->name('index');
            Route::post('edit', [ProfileController::class, 'edit'])->name('edit');
        });
    });

    Route::middleware('auth.role:operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('/', [OperatorDashboardController::class, 'index'])->name('index');
        Route::post('/', [OperatorDashboardController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
        Route::post('/downloadDokumen', [OperatorDashboardController::class, 'downloadDokumen'])->name('downloadDokumen');
        Route::get('loadDokumen', [OperatorDokumenController::class, 'loadDokumen'])->name('loadDokumen');

        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/', [OperatorDokumenController::class, 'index'])->name('index');
            $master_bagian_kandir = \App\Models\MasterBagian::where('tipe', 'kandir');
            Route::get('{jenis_file}/{bagian?}', [OperatorDokumenController::class, 'index_by_jenis'])->whereIn('bagian', $master_bagian_kandir->pluck('kode_bagian')->toArray())->name('index_by_jenis');
            Route::post('simpan', [OperatorDokumenController::class, 'simpan'])->name('simpan');
            Route::post('edit', [OperatorDokumenController::class, 'edit'])->name('edit');
            Route::post('check-nomor', [OperatorDokumenController::class, 'check_nomor'])->name('check-nomor');
            Route::post('getDokumenPerubahan', [OperatorDokumenController::class, 'getDokumenPerubahan'])->name('getDokumenPerubahan');
            Route::post('tampil', [OperatorDokumenController::class, 'tampil'])->name('tampil');
            Route::post('tampil-v1', [OperatorDokumenController::class, 'tampil_v1'])->name('tampil_v1');
            Route::get('preview', [OperatorDokumenController::class, 'preview'])->name('preview');
            Route::post('getDataDokumen', [OperatorDokumenController::class, 'getDataDokumen'])->name('getDataDokumen');
            Route::post('getDataDokumenByJenis', [OperatorDokumenController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
            Route::post('getDokumenHistory', [OperatorDokumenController::class, 'getDokumenHistory'])->name('getDokumenHistory');
            Route::post('getPerubahanDokumenHistory', [OperatorDokumenController::class, 'getPerubahanDokumenHistory'])->name('getPerubahanDokumenHistory');
            Route::get('loadDokumen', [OperatorDokumenController::class, 'loadDokumen'])->name('loadDokumen');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('', [OperatorProfileController::class, 'index'])->name('index');
            Route::post('edit', [OperatorProfileController::class, 'edit'])->name('edit');
            Route::post('edit-email', [OperatorProfileController::class, 'edit_email'])->name('edit-email');
            Route::post('edit-nohp', [OperatorProfileController::class, 'edit_nohp'])->name('edit-nohp');
        });

    });

    Route::middleware('auth.role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'index'])->name('index');
        Route::post('/', [UserDashboardController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
        Route::post('/downloadDokumen', [UserDashboardController::class, 'downloadDokumen'])->name('downloadDokumen');
        Route::get('loadDokumen', [UserDokumenController::class, 'loadDokumen'])->name('loadDokumen');

        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/', [UserDokumenController::class, 'index'])->name('index');
            $master_bagian_kandir = \App\Models\MasterBagian::where('tipe', 'kandir');
            Route::get('{jenis_file}/{bagian?}', [UserDokumenController::class, 'index_by_jenis'])->whereIn('bagian', $master_bagian_kandir->pluck('kode_bagian')->toArray())->name('index_by_jenis');
            // Route::post('simpan', [UserDokumenController::class, 'simpan'])->name('simpan');
            Route::post('tampil', [UserDokumenController::class, 'tampil'])->name('tampil');
            Route::get('preview', [UserDokumenController::class, 'preview'])->name('preview');
            Route::post('getDataDokumen', [UserDokumenController::class, 'getDataDokumen'])->name('getDataDokumen');
            Route::post('getDataDokumenByJenis', [UserDokumenController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
            Route::post('getPerubahanDokumenHistory', [UserDokumenController::class, 'getPerubahanDokumenHistory'])->name('getPerubahanDokumenHistory');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('', [UserProfileController::class, 'index'])->name('index');
            Route::post('edit', [UserProfileController::class, 'edit'])->name('edit');
            Route::post('edit-email', [UserProfileController::class, 'edit_email'])->name('edit-email');
            Route::post('edit-nohp', [UserProfileController::class, 'edit_nohp'])->name('edit-nohp');
        });
    });

    Route::middleware('auth.role:tamu')->prefix('tamu')->name('tamu.')->group(function () {
        Route::get('/', [TamuDashboardController::class, 'index'])->name('index');
        Route::post('/', [TamuDashboardController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
        Route::post('/downloadDokumen', [TamuDashboardController::class, 'downloadDokumen'])->name('downloadDokumen');
        Route::get('loadDokumen', [TamuDokumenController::class, 'loadDokumen'])->name('loadDokumen');

        Route::prefix('dokumen')->name('dokumen.')->group(function () {
            Route::get('/', [TamuDokumenController::class, 'index'])->name('index');
            $master_bagian_kandir = \App\Models\MasterBagian::where('tipe', 'kandir');
            Route::get('{jenis_file}/{bagian?}', [TamuDokumenController::class, 'index_by_jenis'])->whereIn('bagian', $master_bagian_kandir->pluck('kode_bagian')->toArray())->name('index_by_jenis');
            // Route::post('simpan', [UserDokumenController::class, 'simpan'])->name('simpan');
            Route::post('tampil', [TamuDokumenController::class, 'tampil'])->name('tampil');
            Route::get('preview', [TamuDokumenController::class, 'preview'])->name('preview');
            Route::post('getDataDokumen', [TamuDokumenController::class, 'getDataDokumen'])->name('getDataDokumen');
            Route::post('getDataDokumenByJenis', [TamuDokumenController::class, 'getDataDokumenByJenis'])->name('getDataDokumenByJenis');
            Route::post('getPerubahanDokumenHistory', [TamuDokumenController::class, 'getPerubahanDokumenHistory'])->name('getPerubahanDokumenHistory');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('', [TamuProfileController::class, 'index'])->name('index');
            Route::post('edit', [TamuProfileController::class, 'edit'])->name('edit');
            Route::post('edit-email', [TamuProfileController::class, 'edit_email'])->name('edit-email');
            Route::post('edit-nohp', [TamuProfileController::class, 'edit_nohp'])->name('edit-nohp');
        });
    });
});


Route::prefix('development')->name('development.')->middleware(['auth', 'auth.role:admin'])->group(function(){
    Route::get('preview', [DevelopmentController::class, 'preview'])->name('preview');
    Route::get('test', function () {
        $tes = new Watermark(public_path('storage/0oYpv88hKiAZkl0KnfSIFxlCdrLO5uZKJ9dBvG9t.pdf'));
        $watermark = new Watermark(public_path('storage/0oYpv88hKiAZkl0KnfSIFxlCdrLO5uZKJ9dBvG9t.pdf'));

        // Watermark with text
        $watermark->setFont('Arial');
        $watermark->setFontSize(18);
        $watermark->setRotate(345);
        $watermark->setOffset(20, 60);
        $watermark->setPosition(Watermark::POSITION_BOTTOM_RIGHT);

        $text = "ajaxray.com";
        $watermark->withText($text, public_path('storage/tes.pdf'));
        dd($watermark, public_path('storage/tes.pdf'));
        return response()->file($watermark);
    });
    Route::get('tes1', [DevelopmentController::class, 'tes1'])->name('tes1');
    Route::get('tes2', [DevelopmentController::class, 'tes2'])->name('tes2');
    Route::get('fill-dokumen-history-perubahan', [DevelopmentController::class, 'fill_dhp']);
    Route::get('test-comma-separated-relationship', [DevelopmentController::class, 'comma'])->name('comma');
    Route::get('send-email', function(){
        Illuminate\Support\Facades\Mail::to('arbihasnoto@gmail.com')->send(new \App\Mail\LoginMessageMail('a', 'b', 'c', 'd', 'e'));
    });
    Route::get('email-template', function(){
        return view('email_update_password_template', [
            'nama' => 'nama',
            'tanggal' => 'tanggal',
            'ipaddress' => 'ipaddress',
            'device' => 'device',
            'os' => 'os'
        ]);
    });

    Route::get('normalize-dokumen-row', [DevelopmentController::class, 'normalize']);

    Route::get('pdf-1.7', [DevelopmentController::class, 'pdf17']);

    Route::get('check-file/{url}', [DevelopmentController::class, 'check_file'])->name('check_file');
});
