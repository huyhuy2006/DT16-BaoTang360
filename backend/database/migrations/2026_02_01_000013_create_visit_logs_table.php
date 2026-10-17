<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id');
            $table->foreignId('scene_id')->nullable()->constrained('scenes')->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained('hotspots')->nullOnDelete();
            $table->enum('action', ['view_scene', 'click_hotspot', 'play_narration', 'start_tour', 'finish_tour']);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['scene_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
