<?php

use App\Models\Dokumen;
use Carbon\Carbon;

function getNextDocument($dokumen_base_id, $tanggal_dokumen){
    $dokumen_next_id = null;
    $listDokumen = Dokumen::where('dokumen_base_id', $dokumen_base_id)->orderBy('tanggal', 'asc')->get();
    $listDokumen->each(function($item, $key) use ($dokumen_next_id,$tanggal_dokumen){
        if(Carbon::parse($tanggal_dokumen) < Carbon::parse($item->tanggal)){
            $dokumen_next_id = $item->id;
        }
    });
    return $dokumen_next_id;
}
