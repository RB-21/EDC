<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenHistory extends Model
{
    use HasFactory;
    protected $table = 'dokumen_history';
    protected $guarded = [];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function dokumen(){
        return $this->hasOne(Dokumen::class, 'id', 'dokumen_id');
    }

    public function aksi(){
        return $this->hasOne(MasterJenisAksi::class, 'id', 'jenis_aksi_id');
    }
}
