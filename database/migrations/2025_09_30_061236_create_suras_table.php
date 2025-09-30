<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suras', function (Blueprint $table) {
            $table->id();
            $table->integer('sura_number')->unique(); // e.g., 1, 2, 3...
            $table->string('arabic_name');                // Arabic name (الفاتحة)
            $table->string('german_name');                // English name (Al-Fatihah)
            $table->integer('total_ayas');            // number of verses
            $table->string('revelation_type');        // Meccan / Medinan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suras');
    }
};
