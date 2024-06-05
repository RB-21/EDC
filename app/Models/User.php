<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function level(){
        return $this->hasOne(MasterUserLevel::class, 'level', 'user_level');
    }

    public function uRole(){
        return $this->hasOne(MasterUserRole::class, 'kode', 'role');
    }

    public function uBagian(){
        return $this->hasOne(MasterBagian::class, 'kode_bagian', 'bagian');
    }

    public function uBiasaDetail(){
        return $this->hasOne(UserBiasaDetail::class, 'user_id', 'id');
    }

    public function uTamuDetail(){
        return $this->hasOne(UserTamuDetail::class, 'user_id', 'id');
    }

    public function dokumenHistory(){
        return $this->hasMany(DokumenHistory::class, 'user_id', 'id');
    }

    public function jenisAksi(){
        return $this->hasMany(MasterJenisAksi::class, 'id', 'jenis_aksi');
    }

    public function getAksi(){
        $monitorId = $this->getOriginal('jenisAksi');
        return MasterJenisAksi::whereIn('id', explode(',', $monitorId))->get();
    }
}
