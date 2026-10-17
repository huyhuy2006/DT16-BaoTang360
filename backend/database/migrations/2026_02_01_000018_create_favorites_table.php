<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('target_type', ['artifact', 'tour']);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();
            $table->unique(['user_id', 'target_type', 'target_id'], 'favorites_user_target_unique');
            $table->index(['target_type', 'target_id'], 'favorites_target_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
