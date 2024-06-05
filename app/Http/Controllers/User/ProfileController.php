<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\UpdatePasswordMessageMail;
use App\Models\MasterJenisAksi;
use App\Models\MasterJenisFile;
use App\Models\User;
use App\Models\UserBiasaDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use hisorange\BrowserDetect\Parser as Browser;

class ProfileController extends Controller
{
    public function index(){
        $user_jenis_file = MasterJenisFile::whereIn('kode', explode(',',auth()->user()->jenis_file))->pluck('singkatan')->join(' | ');
        $user_jenis_aksi = MasterJenisAksi::whereIn('id', explode(',', auth()->user()->jenis_aksi))->pluck('nama')->join(' | ');
        return view('user.profile.index', compact('user_jenis_file', 'user_jenis_aksi'));
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

        $device = Browser::deviceType();
        $os = Browser::platformFamily();

        if (!empty($user->uBiasaDetail)) {
            if (!empty($user->uBiasaDetail->no_hp)) {
                $this->sendMessage2($user->uBiasaDetail->no_hp,$user->name, $request->ip(), $device, $os, 'penggantian password');
            }
        }
        if (!empty($user->uTamuDetail)) {
            if (!empty($user->uTamuDetail->no_hp)) {
                $this->sendMessage2($user->uTamuDetail->no_hp,$user->name, $request->ip(), $device, $os, 'penggantian password');
            }
        }
        // if(!empty($user->email)){
        //     Mail::to($user->email)->send(new UpdatePasswordMessageMail($user->name, Carbon::now(), $request->ip(), $device, $os));
        // }

        return back()->with([
            'success' => true,
            'message' => 'Berhasil Mengupdate Password'
        ]);
    }

    public function edit2(Request $request){
        $request->validate([
            'email' => 'required|email',
            'nohp' => 'required|numeric|digits_between:10,13'
        ]);
        $user = User::findOrFail(auth()->user()->id);
        $user->update(['email' => $request->email]);
        $user->uBiasaDetail->update(['no_hp' => $request->nohp]);
        return back()->with(['success' => true, 'message' => 'Berhasil Mengupdate Data']);
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

    public function sendMessage2($phone, $nama, $ipaddress, $device, $os, $aksi)
    {
        $message_title = "EDC (Enterprise Document Control)\n";
        $message_content = "Yth. $nama \n";
        $message_content .= "Notifikasi Keamanan - ". ucwords($aksi) ." \n\n";
        $message_content .= "Tanggal akses : " . Carbon::now()->toDateTimeString() . " WIB\n";
        $message_content .= "IP Address : " . $ipaddress."\n";
        $message_content .= "Perangkat : " . $device."\n";
        $message_content .= "Sistem Operasi : " . $os."\n\n";
        $message_content .= "Sistem mendeteksi adanya $aksi ke Akun Aplikasi EDC Anda.\n";
        $message_content .= "Jika ini memang Anda, Anda tidak perlu melakukan apa-apa.\n";
        $message_content .= "Jika bukan, silahkan ubah segera password dan hubungi Sub Bagian Pengadaan dan TI.\n";
        $message_content .= "Terima Kasih.";
        $curl = curl_init();
        $token = "KXCwBNP19Q3L5O7AlNR3IXGMlZnYjUyCZRkg1uH916uRpIwKaXlNCXc2QvoeeuzH";
        $payload = [
            "data" => [
                [
                    'phone' => $phone,
                    'message' => [
                        'title' => [
                            'type' => 'text',
                            'content' => $message_title,
                        ],
                        'buttons' => [
                            'url' => [
                                'display' => 'Buka Aplikasi EDC',
                                'link' => 'http://edc.ptpn4.com',
                            ]
                        ],
                        'content' => $message_content,
                    ],
                ]
            ]
        ];
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
                "Content-Type: application/json"
            )
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_URL,  "https://pati.wablas.com/api/v2/send-template");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        return 1;
    }
}
