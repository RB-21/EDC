<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckStatusActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:active_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereNot('role', 'adm')->get();
        $tanggal_hari_ini = Carbon::today();
        foreach($users as $user){
            $tanggal_aktif_awal_user = Carbon::parse($user->active_from);
            $tanggal_aktif_sampai_user = Carbon::parse($user->active_to);
            if(!($tanggal_hari_ini >= $tanggal_aktif_awal_user && $tanggal_hari_ini <= $tanggal_aktif_sampai_user)){
                $user->update([
                    'active_status' => 0
                ]);
            }
        }
        return Command::SUCCESS;
    }
}
