<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public const RULE_NAME_REQUIRED = 'required';
    public const RULE_NAME_STRING = 'string';
    public const RULE_NAME_MAX = 'max:255';
    public const RULE_PROJECT_EXISTS = 'exists:' . Project::TABLE . ',' . Project::COLUMN_ID;
    public const RULE_PRIORITY_INTEGER = 'integer';
    public const RULE_PRIORITY_MIN = 'min:' . Task::PRIORITY_MIN;
    public const RULE_PRIORITY_MAX = 'max:' . Task::PRIORITY_MAX;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Task::COLUMN_NAME => [
                self::RULE_NAME_REQUIRED,
                self::RULE_NAME_STRING,
                self::RULE_NAME_MAX,
            ],
            Task::COLUMN_PROJECT_ID => [
                self::RULE_NAME_REQUIRED,
                self::RULE_PROJECT_EXISTS,
            ],
            Task::COLUMN_PRIORITY => [
                'sometimes',
                self::RULE_PRIORITY_INTEGER,
                self::RULE_PRIORITY_MIN,
                self::RULE_PRIORITY_MAX,
            ],
        ];
    }
}