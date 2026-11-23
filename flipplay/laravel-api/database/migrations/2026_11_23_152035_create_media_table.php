
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->year('release_year')->nullable();
            $table->json('genres')->nullable();
            $table->string('type')->default('Anime');
            $table->unsignedInteger('episodes')->nullable();
            $table->decimal('score', 3, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
