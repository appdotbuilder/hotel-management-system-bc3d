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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique()->comment('Room number identifier');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('floor')->comment('Floor number or level');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'out_of_order'])->default('available')->comment('Current room status');
            $table->text('notes')->nullable()->comment('Additional notes about the room');
            $table->timestamps();
            
            $table->index('room_number');
            $table->index('status');
            $table->index(['status', 'room_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};