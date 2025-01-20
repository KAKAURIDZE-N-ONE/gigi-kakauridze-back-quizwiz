<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'icon_color',
        'background_color',
        'active_background_color',
    ];

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

}
