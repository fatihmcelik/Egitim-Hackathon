<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['digital', 'physical']); // Dijital mi, Yüz yüze mi?
            $table->string('location_or_link')->nullable(); // Discord linki veya Kütüphane adı
            $table->dateTime('scheduled_at'); // Ne zaman buluşacaklar?
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};