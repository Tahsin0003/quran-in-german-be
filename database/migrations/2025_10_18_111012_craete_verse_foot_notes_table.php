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
        // Schema::create('verse_foot_notes', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('verse_id')->references('index')->on('quran_text')->onDelete('cascade');
        //     $table->string('title')->nullable();
        //     $table->text('content')->nullable();
        //     $table->integer('order')->nullable();
        //     $table->boolean('status')->default(0);
        //     $table->timestamps();
        // });
        Schema::create('verse_foot_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('verse_id'); // matches int(4)
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();

            $table->foreign('verse_id')
                ->references('index')
                ->on('quran_text')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verse_foot_notes');
    }
};
