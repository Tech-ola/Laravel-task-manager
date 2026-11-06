<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public const RULE_NAME_REQUIRED = 'required';
    public const RULE_NAME_STRING   = 'string';
    public const RULE_NAME_MAX      = 'max:' . Project::NAME_MAX_LENGTH;
    public const RULE_NAME_UNIQUE   = 'unique:' . Project::TABLE . ',' . Project::COLUMN_NAME;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Project::COLUMN_NAME => [
                self::RULE_NAME_REQUIRED,
                self::RULE_NAME_STRING,
                self::RULE_NAME_MAX,
                self::RULE_NAME_UNIQUE,
            ],
        ];
    }
}