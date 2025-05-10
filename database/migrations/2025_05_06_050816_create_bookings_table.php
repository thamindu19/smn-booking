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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
                $table->foreignId('poya_day_id')->constrained()->onDelete('cascade');
                $table->string('name', 150);
                $table->string('email', 254);
                $table->string('phone', 20);
                $table->text('notes');
                $table->enum('status', ['pending', 'approved', 'rejected']);
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
