<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Project::TABLE, function (Blueprint $table) {
            $table->id(Project::COLUMN_ID);
            $table->string(Project::COLUMN_NAME, Project::NAME_MAX_LENGTH);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Project::TABLE);
    }
};