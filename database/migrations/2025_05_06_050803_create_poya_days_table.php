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
        Schema::create('poya_days', function (Blueprint $table) {
            $table->id();
            $table->string('month', 50);
            $table->date('date');
            $table->unsignedBigInteger('booking_id')->nullable()->unique()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poya_days');
    }
};
