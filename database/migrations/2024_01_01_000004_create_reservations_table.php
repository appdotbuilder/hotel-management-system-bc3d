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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique()->comment('Unique reservation identifier');
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->date('check_in_date')->comment('Check-in date');
            $table->date('check_out_date')->comment('Check-out date');
            $table->integer('adults')->default(1)->comment('Number of adult guests');
            $table->integer('children')->default(0)->comment('Number of child guests');
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending')->comment('Reservation status');
            $table->decimal('total_amount', 10, 2)->comment('Total reservation amount');
            $table->text('special_requests')->nullable()->comment('Special requests from guest');
            $table->text('notes')->nullable()->comment('Internal notes');
            $table->timestamp('checked_in_at')->nullable()->comment('Actual check-in timestamp');
            $table->timestamp('checked_out_at')->nullable()->comment('Actual check-out timestamp');
            $table->timestamps();
            
            $table->index('reservation_number');
            $table->index('status');
            $table->index(['check_in_date', 'check_out_date']);
            $table->index(['status', 'check_in_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};