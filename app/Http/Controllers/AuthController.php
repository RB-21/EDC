<?php

namespace App\Http\Controllers;

use App\Mail\LoginMessageMail;
use App\Mail\UpdatePasswordMessageMail;
use App\Models\MasterUserRole;
use App\Models\ProhibitedPassword;
use App\Models\User;
use App\Models\UserLoginHistory;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function proses_login(Request $request)
    {
        $checkLimit = $this->isLimitAttempLogin($request);
        if($checkLimit){
            return redirect()->route('login')->with(['success' => false, 'message' => $checkLimit]);
        }
        $request->validate([
            'nik' => 'required',
            'password' => 'required|between:6,15'
        ], [
            'password.between' => 'Password harus berjumlah antara :min dan :max buah.'
        ]);

        $list_prohibited_password = ProhibitedPassword::lazy()->pluck('value');

        $user = User::where('nik', $request->nik)->first();
        if (empty($user)) {
            return back()->with(['success' => false, 'message' => 'User Tidak Terdaftar']);
        }
        if (!Hash::check($request->password, $user->password)) {
            return back()->with(['success' => false, 'message' => 'NIK atau Password Salah']);
        }
        if ($user->role != 'adm') {
            $tanggal_hari_ini = Carbon::today();
            $tanggal_aktif_awal_user = Carbon::parse($user->active_from);
            $tanggal_aktif_sampai_user = Carbon::parse($user->active_to);
            if (!($tanggal_hari_ini >= $tanggal_aktif_awal_user && $tanggal_hari_ini <= $tanggal_aktif_sampai_user && $user->active_status == true)) {
                $user->update([
                    'active_status' => 0
                ]);
                return back()->with(['success' => false, 'message' => 'Akun Tidak AKtif, Silakan Hubungi Admin']);
            }
        }
        session()->regenerate();
        Auth::login($user);
        if ($request->password == auth()->user()->nik || in_array($request->password, $list_prohibited_password->toArray())) {
            return redirect()->route('update_password')->with('update_password', true);
        }

        // dd(
        //     Browser::userAgent(),
        //     Browser::deviceType(),
        //     Browser::browserName(),
        //     Browser::browserFamily(),
        //     Browser::browserVersion(),
        //     Browser::platformName(),
        //     Browser::platformFamily(),
        //     Browser::platformVersion(),
        // );

        // $agent = new Agent();
        // $device = $agent->device();
        // $os = $agent->platform();
        // $os_version = $agent->version($os);
        // $browser = $agent->browser();
        // $browser_version =  $agent->version($browser);

        $device = Browser::deviceType();
        $os = Browser::platformFamily();
        $os_version = Browser::platformVersion();
        $browser = Browser::browserFamily();
        $browser_version = Browser::browserVersion();
        UserLoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'device' => $device,
            'os' => $os,
            'os_version' => $os_version,
            'browser' => $browser,
            'browser_version' => $browser_version,
        ]);

        if (!empty($user->uBiasaDetail)) {
            if (!empty($user->uBiasaDetail->no_hp)) {
                $this->sendMessage2($user->uBiasaDetail->no_hp,$user->name, $request->ip(), $device, $os, 'login terbaru');
            }
        }
        if (!empty($user->uTamuDetail)) {
            if (!empty($user->uTamuDetail->no_hp)) {
                $this->sendMessage2($user->uTamuDetail->no_hp,$user->name, $request->ip(), $device, $os, 'login terbaru');
            }
        }

        // if(!empty($user->email)){
        //     Mail::to($user->email)->send(new LoginMessageMail($user->name, $request->ip(), Carbon::now( ),  $device, $os));
        // }

        $master_user_role = MasterUserRole::all();
        foreach ($master_user_role as $role) {
            if ($role->kode == $user->role) {
                return redirect()->route($role->redirect_route);
            }
        }
        return back()->with(['success' => false, 'message' => 'Silakan Hubungi Admin']);
    }

    public function lupa_password(){
        return view('lupa_password');
    }

    public function konfirmasi_lupa_password(Request $request){
        // dd($request->all());

        $request->validate([
            'nik' => 'numeric',
            'nohp' => 'numeric|digits_between:10,13'
        ], [
            'nik.numeric' => 'Nik Harus Berupa Angka',
            'nohp.numeric' => 'Nomor HP Harus Berupa Angka',
            'nohp.digits_between' => 'Nomor HP harus dalam rentang 10 sampai 13 Digit'
        ]);

        $user = User::where('nik', $request->nik)->first();

        if(empty($user)){
            return back()->with(['success' => false, 'message' => 'User Tidak Ditemukan']);
        }

        $user_nohp = $user-> uBiasaDetail->no_hp ?? $user->uTamuDetail->no_hp;

        if(empty($user_nohp)){
            return back()->with(['success' => false, 'message' => 'No HP Belum didaftarkan silakan hubungi admin']);
        }

        if($user_nohp != $request->nohp){
            return back()->with(['success' => false, 'message' => 'No HP Tidak Sesuai']);
        }

        $aksi = 'Reset Password';

        $password_baru = random_int(11111111, 99999999);
        $user->update(['password' => Hash::make($password_baru)]);

        $device = Browser::deviceType();
        $os = Browser::platformFamily();
        $os_version = Browser::platformVersion();
        $browser = Browser::browserFamily();
        $browser_version = Browser::browserVersion();

        $this->sendMessageLupaPassword($user_nohp, $user->name, $aksi, $password_baru, $request->ip(), $device, $os);
        UserLoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'device' => $device,
            'os' => $os,
            'os_version' => $os_version,
            'browser' => $browser,
            'browser_version' => $browser_version,
            'aksi' => $aksi
        ]);


        return redirect()->route('login')->with(['success' => true, 'message' => 'Password Berhasil Direset, dan Password baru telah dikirimkan ke Whatsapp Anda']);
        dd($request->all() ,$user_nohp);
    }

    public function sendMessageLupaPassword($phone, $nama, $aksi, $password_baru, $ipaddress, $device, $os)
    {

        $message_content = "*EDC (Enterprise Document Control)*\n\n";
        $message_content .= "Yth. $nama \n";
        $message_content .= "Notifikasi Keamanan - ". ucwords($aksi) ." \n\n";
        $message_content .= "Tanggal akses : " . Carbon::now()->toDateTimeString() . " WIB\n";
        $message_content .= "IP Address : " . $ipaddress."\n";
        $message_content .= "Perangkat : " . $device."\n";
        $message_content .= "Sistem Operasi : " . $os."\n\n";
        $message_content .= "Sistem mendeteksi adanya $aksi pada Akun Aplikasi EDC Anda.\n";
        $message_content .= "Jika ini memang Anda, Silakan login ke aplikasi:\n\n";
        $message_content .= "*Password Baru : $password_baru*\n\n";
        $message_content .= "Jika bukan, silahkan ubah segera password dan hubungi Sub Bagian Teknologi Informasi.\n";
        $message_content .= "Terima Kasih.\n\n";
        $message_content .= "Silakan akses EDC: https://edc.ptpn4.com";
        $curl = curl_init();
        $token = "KXCwBNP19Q3L5O7AlNR3IXGMlZnYjUyCZRkg1uH916uRpIwKaXlNCXc2QvoeeuzH";
        $payload = [
            "data" => [
                [
                    'phone' => $phone,
                    'message' => $message_content
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
        curl_setopt($curl, CURLOPT_URL,  "https://pati.wablas.com/api/v2/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        return 1;
    }

    public function update_password()
    {
        // if (request()->session()->has('update_password')) {
            return view('update_password');
        // }
        $this->logout();
        return redirect()->route('login');
    }

    public function update_password_process(Request $request)
    {
        // $request->validate([
        //     'password' => 'required|min:8|max:15',
        //     'password_confirmation' => 'required|same:password'
        // ]);


        if (empty($request->password) || empty($request->password_confirmation)) {
            return redirect()->route('update_password')->with(['update_password' => true, 'success' => false, 'message' => 'Password Harus Diisi']);
        }

        if ($request->password !== $request->password_confirmation) {
            return redirect()->route('update_password')->with(['update_password' => true, 'success' => false, 'message' => 'Password dan Confirmasi Tidak Sama']);
        }

        $list_prohibited_password = ProhibitedPassword::lazy()->pluck('value');
        if ($request->password == auth()->user()->nik || in_array($request->password, $list_prohibited_password->toArray())) {
            return redirect()->route('update_password')->with(['update_password' => true, 'success' => false, 'message' => 'Gunakan Password Lain']);
        }

        $user = User::findOrFail(auth()->user()->id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);


        $device = Browser::deviceType();
        $os = Browser::platformFamily();
        $os_version = Browser::platformVersion();
        $browser = Browser::browserFamily();
        $browser_version = Browser::browserVersion();
        // UserLoginHistory::create([
        //     'user_id' => $user->id,
        //     'ip_address' => $request->ip(),
        //     'device' => $device,
        //     'os' => $os,
        //     'os_version' => $os_version,
        //     'browser' => $browser,
        //     'browser_version' => $browser_version,
        // ]);

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
        $master_user_role = MasterUserRole::all();
        foreach ($master_user_role as $role) {
            if ($role->kode == $user->role) {
                return redirect()->route($role->redirect_route);
            }
        }
        $this->logout();
        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        session()->regenerate();
        return redirect()->route('login');
    }

    public function sendMessage($phone, $ipaddress, $device, $os, $url)
    {
        $message_title = 'EDC (Enterprise Document Control)\n\n';
        $message_content = 'Notifikasi Keamanan - Login Terbaru \n\n';
        $message_content .= 'Tanggal akses : ' . Carbon::now()->toDateTimeString() . ' WIB';
        $message_content .= 'IP Address : ' . $ipaddress;
        $message_content .= 'Perangkat : ' . $device;
        $message_content .= 'Sistem Operasi : ' . $os . '\n\n';
        $message_content .= 'Sistem mendeteksi adanya login baru pada Akun Aplikasi EDC Anda.\n';
        $message_content .= 'Jika ini memang Anda, Anda tidak perlu melakukan apa-apa.';
        $message_content .= 'Jika bukan, silahkan ubah segera password dan hubungi Sub Bagian Teknologi Informasi.';
        $message_content .= 'Terima Kasih.';
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'KXCwBNP19Q3L5O7AlNR3IXGMlZnYjUyCZRkg1uH916uRpIwKaXlNCXc2QvoeeuzH'
        ])->post('https://pati.wablas.com/api/v2/send-template', [
            'phone' => $phone,
            'message' => [
                'title' => [
                    'type' => 'text',
                    'content' => $message_title
                ],
                'content' => $message_content,
                'buttons' => [
                    'url' => [
                        'display' => 'Buka Aplikasi EDC',
                        'link' => $url
                    ]
                ]
            ],
            'secret' => false,
            'priority' => true,
        ]);
        return 1;
    }

    public function sendMessage2($phone, $nama, $ipaddress, $device, $os, $aksi)
    {

        $message_content = "*EDC (Enterprise Document Control)*\n\n";
        $message_content .= "Yth. $nama \n";
        $message_content .= "Notifikasi Keamanan - ". ucwords($aksi) ." \n\n";
        $message_content .= "Tanggal akses : " . Carbon::now()->toDateTimeString() . " WIB\n";
        $message_content .= "IP Address : " . $ipaddress."\n";
        $message_content .= "Perangkat : " . $device."\n";
        $message_content .= "Sistem Operasi : " . $os."\n\n";
        $message_content .= "Sistem mendeteksi adanya $aksi pada Akun Aplikasi EDC Anda.\n";
        $message_content .= "Jika ini memang Anda, Anda tidak perlu melakukan apa-apa.\n";
        $message_content .= "Jika bukan, silahkan ubah segera password dan hubungi Sub Bagian Teknologi Informasi.\n";
        $message_content .= "Terima Kasih.\n\n";
        $message_content .= "Silakan akses EDC: https://edc.ptpn4.com";
        $curl = curl_init();
        $token = "KXCwBNP19Q3L5O7AlNR3IXGMlZnYjUyCZRkg1uH916uRpIwKaXlNCXc2QvoeeuzH";
        $payload = [
            "data" => [
                [
                    'phone' => $phone,
                    'message' => $message_content
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
        curl_setopt($curl, CURLOPT_URL,  "https://pati.wablas.com/api/v2/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        return 1;
    }

    public function throttleKey($request){
        return Str::transliterate(Str::lower($request->input('nik')).'|'.$request->ip());
    }

    public function isLimitAttempLogin(Request $request){
        if(!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)){
            RateLimiter::hit($this->throttleKey($request));
            return false;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        return 'Coba lagi dalam '. $seconds .' detik.';
    }
}
