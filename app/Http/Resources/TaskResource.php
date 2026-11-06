<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public const FIELD_ID = Task::COLUMN_ID;
    public const FIELD_PROJECT_ID = Task::COLUMN_PROJECT_ID;
    public const FIELD_NAME = Task::COLUMN_NAME;
    public const FIELD_PRIORITY = Task::COLUMN_PRIORITY;
    public const FIELD_CREATED_AT = Task::COLUMN_CREATED_AT;
    public const FIELD_UPDATED_AT = Task::COLUMN_UPDATED_AT;

    public function toArray(Request $request): array
    {
        return [
            self::FIELD_ID => $this->{self::FIELD_ID},
            self::FIELD_PROJECT_ID => $this->{self::FIELD_PROJECT_ID},
            self::FIELD_NAME => $this->{self::FIELD_NAME},
            self::FIELD_PRIORITY => $this->{self::FIELD_PRIORITY},
            self::FIELD_CREATED_AT => $this->{self::FIELD_CREATED_AT},
            self::FIELD_UPDATED_AT => $this->{self::FIELD_UPDATED_AT},
        ];
    }
}