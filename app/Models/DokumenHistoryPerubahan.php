<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenHistoryPerubahan extends Model
{
    use HasFactory;
    protected $table = 'dokumen_history_perubahan';
    protected $guarded = [];
}
