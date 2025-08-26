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
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->enum('task_type', ['cleaning', 'maintenance', 'inspection', 'restocking'])->comment('Type of housekeeping task');
            $table->string('priority', 10)->default('medium')->comment('Task priority level');
            $table->text('description')->comment('Task description');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->comment('Task status');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('scheduled_at')->nullable()->comment('When task is scheduled');
            $table->timestamp('started_at')->nullable()->comment('When task was started');
            $table->timestamp('completed_at')->nullable()->comment('When task was completed');
            $table->text('completion_notes')->nullable()->comment('Notes after task completion');
            $table->timestamps();
            
            $table->index('status');
            $table->index(['room_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_tasks');
    }
};