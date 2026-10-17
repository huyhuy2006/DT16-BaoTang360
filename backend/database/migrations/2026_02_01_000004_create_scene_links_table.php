<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scene_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_scene_id')->constrained('scenes')->cascadeOnDelete();
            $table->foreignId('to_scene_id')->constrained('scenes')->cascadeOnDelete();
            $table->float('pitch');
            $table->float('yaw');
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['from_scene_id', 'to_scene_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scene_links');
    }
};
