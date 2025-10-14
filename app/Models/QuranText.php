<?php

namespace App\Models;
use App\Models\Sura;

use Illuminate\Database\Eloquent\Model;

class QuranText extends Model
{
    protected $table = 'quran_text';
    protected $primaryKey = 'index';
    public $timestamps = false;

    public function sura()
    {
        return $this->belongsTo(Sura::class, 'sura', 'sura_number');
    }
}
