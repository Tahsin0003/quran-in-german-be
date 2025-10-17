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
        Schema::create('sura_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sura_id')->constrained('suras')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
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
        Schema::dropIfExists('sura_summaries');
    }
};
