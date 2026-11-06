<?php

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Task::TABLE, function (Blueprint $table) {
            $table->id(Task::COLUMN_ID);

            $table->foreignId(Task::COLUMN_PROJECT_ID)
                  ->constrained(Project::TABLE, Project::COLUMN_ID)
                  ->cascadeOnDelete();

            $table->string(Task::COLUMN_NAME);
            $table->unsignedInteger(Task::COLUMN_PRIORITY)
                  ->default(Task::DEFAULT_PRIORITY);

            $table->timestamps();

            $table->index([Task::COLUMN_PROJECT_ID, Task::COLUMN_PRIORITY]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Task::TABLE);
    }
};