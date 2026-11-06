<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Website Redesign', 'Mobile App', 'Marketing Campaign'] as $name) {
            Project::firstOrCreate([Project::COLUMN_NAME => $name]);
        }
    }
}