<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public const TABLE = 'projects';
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const NAME_MAX_LENGTH = 255;

    protected $table = self::TABLE;

    protected $fillable = [
        self::COLUMN_NAME,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}