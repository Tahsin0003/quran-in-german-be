<?php

namespace App\Models;
use App\Models\Sura;

use Illuminate\Database\Eloquent\Model;

class QuranText extends Model
{
    protected $table = 'quran_text';

    public function sura()
    {
        return $this->belongsTo(Sura::class, 'sura', 'sura_number');
    }
}
