<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderTasksRequest extends FormRequest
{
    public const RULE_ORDERED_IDS_REQUIRED = 'required';
    public const RULE_ORDERED_IDS_ARRAY    = 'array';
    public const RULE_ORDERED_IDS_MIN      = 'min:1';

    public const RULE_ITEM_INTEGER = 'integer';

    public const RULE_PROJECT_REQUIRED = 'required';
    public const RULE_PROJECT_INTEGER  = 'integer';
    public const RULE_PROJECT_EXISTS   = 'exists:' . Project::TABLE . ',' . Project::COLUMN_ID;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->input('project_id');

        return [
            'ordered_ids'   => [
                self::RULE_ORDERED_IDS_REQUIRED,
                self::RULE_ORDERED_IDS_ARRAY,
                self::RULE_ORDERED_IDS_MIN,
            ],
            'ordered_ids.*' => [
                self::RULE_ITEM_INTEGER,
                Rule::exists(Task::TABLE, Task::COLUMN_ID)
                    ->where(Task::COLUMN_PROJECT_ID, $projectId),
            ],
            'project_id'    => [
                self::RULE_PROJECT_REQUIRED,
                self::RULE_PROJECT_INTEGER,
                self::RULE_PROJECT_EXISTS,
            ],
        ];
    }
}