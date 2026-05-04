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
        'ai_token_balance' => 'integer',
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

    public function aiChatSessions()
    {
        return $this->hasMany(AiChatSession::class, 'user_id', 'id');
    }

    public function aiChatMessages()
    {
        return $this->hasMany(AiChatMessage::class, 'user_id', 'id');
    }

    public function getAllowedAiModels(array $defaultModels): array
    {
        $raw = trim((string) ($this->ai_allowed_models ?? ''));
        if ($raw === '') {
            return $defaultModels;
        }

        $allowed = array_values(array_filter(array_map('trim', explode(',', $raw))));
        if (empty($allowed)) {
            return $defaultModels;
        }

        $valid = array_values(array_intersect($defaultModels, $allowed));
        return empty($valid) ? $defaultModels : $valid;
    }
}
