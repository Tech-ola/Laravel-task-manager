<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const TABLE = 'tasks';
    public const COLUMN_ID = 'id';
    public const COLUMN_PROJECT_ID = 'project_id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_PRIORITY = 'priority';
    public const COLUMN_CREATED_AT = 'created_at';
    public const COLUMN_UPDATED_AT = 'updated_at';

    public const PRIORITY_MIN = 1;
    public const PRIORITY_MAX = 9999;
    public const DEFAULT_PRIORITY = 1;

    protected $table = self::TABLE;

    protected $fillable = [
        self::COLUMN_PROJECT_ID,
        self::COLUMN_NAME,
        self::COLUMN_PRIORITY,
    ];

    protected $casts = [
        self::COLUMN_PRIORITY => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}