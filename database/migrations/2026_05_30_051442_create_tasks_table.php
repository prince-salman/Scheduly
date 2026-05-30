<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();     // Design, Development, Planning, …
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->date('due_date')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->unsignedInteger('focus_minutes')->default(0); // cumulative logged time
            $table->dateTime('timer_started_at')->nullable();      // null = timer not running
            $table->json('subtasks')->nullable();                  // [{title, done}, …]
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};