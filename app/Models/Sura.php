<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QuranText;

class Sura extends Model
{
    protected $fillable = [
        'sura_number', 'arabic_name', 'german_name', 'total_ayas', 'revelation_type'
    ];
    
    public function ayas()
    {
        // return $this->hasMany(QuranText::class, 'sura', 'id');
        return $this->hasMany(QuranText::class, 'sura', 'sura_number');
    }
}
