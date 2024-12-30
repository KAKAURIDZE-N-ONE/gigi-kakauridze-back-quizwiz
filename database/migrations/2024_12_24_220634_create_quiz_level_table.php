<?php

use App\Models\Level;
use App\Models\Quiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_level', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Quiz::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Level::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_level');
    }
};
