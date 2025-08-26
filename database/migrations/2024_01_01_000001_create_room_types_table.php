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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Room type name (e.g., Single, Double, Suite)');
            $table->text('description')->nullable()->comment('Room type description');
            $table->decimal('base_price', 10, 2)->comment('Base price per night');
            $table->integer('max_occupancy')->comment('Maximum number of guests');
            $table->json('amenities')->nullable()->comment('JSON array of amenities');
            $table->timestamps();
            
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};