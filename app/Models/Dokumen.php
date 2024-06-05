<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;
    protected $table = 'dokumen';
    protected $guarded = [];

    public function dJenisFile(){
        return $this->hasOne(MasterJenisFile::class, 'kode', 'jenis_file_kode');
    }

    public function dBagian(){
        return $this->hasOne(MasterBagian::class, 'kode_bagian', 'bagian');
    }

    public function dHistory(){
        return $this->hasMany(DokumenHistory::class, 'dokumen_id', 'id');
    }

    public function dStatus(){
        return $this->hasOne(MasterStatusDokumen::class, 'id', 'status_dokumen_id');
    }

    public function dStatusPerubahan(){
        return $this->hasOne(MasterStatusPerubahan::class, 'id', 'status_perubahan_id');
    }

    public function dPrev(){
        return $this->hasOne(Dokumen::class, 'id', 'dokumen_prev_id');
    }
    public function dNext(){
        return $this->hasOne(Dokumen::class, 'id', 'dokumen_next_id');
    }
}
