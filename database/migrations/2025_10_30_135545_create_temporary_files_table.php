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
         Schema::create('temporary_files', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->text('download_url');
            $table->text('original_url');
            $table->string('batch_id')->nullable()->index();
            $table->integer('file_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['batch_id', 'expires_at']);
            $table->index(['slug', 'expires_at']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_files');
    }
};
