<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public const FIELD_ID = Project::COLUMN_ID;
    public const FIELD_NAME = Project::COLUMN_NAME;

    public function toArray(Request $request): array
    {
        return [
            self::FIELD_ID => $this->{self::FIELD_ID},
            self::FIELD_NAME => $this->{self::FIELD_NAME},
        ];
    }
}