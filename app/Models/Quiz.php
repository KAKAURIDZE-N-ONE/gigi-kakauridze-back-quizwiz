<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Quiz extends Model
{
    use HasFactory;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'quiz_category', 'quiz_id', 'category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_quiz');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with([
            'questions:id,quiz_id,point',
            'level:id,level,icon_color,background_color',
            'categories:id,name',
        ]);
    }

    public function scopeFilterByUserCompletion(Builder $query, Request $request, $user): Builder
    {
        $query->with(['users' => function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->select('users.id', 'user_quiz.completed_at', 'user_quiz.total_time', 'user_quiz.user_result');
        }]);

        if ($request->has('completed')) {
            $completed = explode(',', $request->query('completed'));
            $hasCompleted = in_array('completed', $completed);
            $hasNotCompleted = in_array('not-completed', $completed);

            if ($hasCompleted && !$hasNotCompleted) {
                $query->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id)
                          ->whereNotNull('user_quiz.completed_at');
                });
            } elseif ($hasNotCompleted && !$hasCompleted) {
                $query->whereDoesntHave('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id)
                          ->whereNotNull('user_quiz.completed_at');
                });
            }
        }

        return $query;
    }


    public function scopeFilterByLevels(Builder $query, $levels): Builder
    {
        $levelsArray = explode(',', $levels);
        return $query->whereIn('level_id', $levelsArray);
    }

    public function scopeFilterByCategories(Builder $query, $categories): Builder
    {
        $categoriesArray = explode(',', $categories);
        return $query->whereHas('categories', function ($query) use ($categoriesArray) {
            $query->whereIn('categories.id', $categoriesArray);
        });
    }

    public function scopeApplySorting(Builder $query, $field, $direction): Builder
    {
        $validFields = ['title', 'total_filled', 'created_at'];
        $validDirections = ['asc', 'desc'];

        if (in_array($field, $validFields) && in_array($direction, $validDirections)) {
            return $query->orderBy($field, $direction);
        }

        return $query;
    }
}
