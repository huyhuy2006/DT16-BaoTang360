<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('target_type', ['artifact', 'tour']);
            $table->unsignedBigInteger('target_id');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->unique(['user_id', 'target_type', 'target_id'], 'feedback_user_target_unique');
            $table->index(['target_type', 'target_id', 'status'], 'feedback_public_lookup_index');
            $table->index(['status', 'created_at'], 'feedback_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
