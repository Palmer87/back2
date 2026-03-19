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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('slug')->unique();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->enum('typePart', ['communique', 'discours', 'interview', 'autre']);
            $table->foreignId('auteur')->constrained('users')->onDelete('cascade');
            $table->boolean('publier')->default(false);
            $table->dateTime('publier_le')->nullable();
            $table->dateTime('retirer_le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
