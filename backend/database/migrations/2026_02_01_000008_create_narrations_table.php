<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('narrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotspot_id')->nullable()->constrained('hotspots')->cascadeOnDelete();
            $table->foreignId('artifact_id')->nullable()->constrained('artifacts')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('audio_path'); // Đường dẫn tương đối trong storage public.
            $table->text('transcript')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narrations');
    }
};
