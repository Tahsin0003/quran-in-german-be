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
        Schema::create('verse_german_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verse_id')->references('index')->on('quran_text')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verse_german_translates');
        
    }
};
