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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prerequisite_region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->integer('order');
            $table->integer('x')->default(0);
            $table->integer('y')->default(0);
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
