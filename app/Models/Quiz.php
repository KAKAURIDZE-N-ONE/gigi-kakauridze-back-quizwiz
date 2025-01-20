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


    protected $fillable = [
        'title',
        'description',
        'duration',
        'total_filled',
        'image',
        'level_id',
        'instructions'
    ];

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
        return $this->belongsToMany(User::class, 'user_quiz')->withPivot('completed_at', 'total_time', 'user_result');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function scopeWithRelations(Builder $query, $user = null): Builder
    {
        $query->with([
            'questions:id,quiz_id,point,question',
            'level:id,level,icon_color,background_color',
            'categories:id,name',
        ]);


        if ($user) {
            $query->with(['users' => function ($query) use ($user) {
                $query->where('users.id', $user->id)
                      ->select('users.id')
                      ->withPivot('completed_at', 'total_time', 'user_result');
            }]);
        }

        return $query;
    }

    public function scopeFilterByUserCompletion(Builder $query, Request $request, $user): Builder
    {
        $query->with(['users' => function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->select('users.id', 'user_quiz.completed_at', 'user_quiz.total_time', 'user_quiz.user_result');
        }]);

        if ($request->has('completed')) {
            $completedStatuses = explode(',', $request->query('completed'));

            if ($this->hasCompletedOnly($completedStatuses)) {
                $this->filterCompleted($query, $user);
            } elseif ($this->hasNotCompletedOnly($completedStatuses)) {
                $this->filterNotCompleted($query, $user);
            }
        }

        return $query;
    }

    private function hasCompletedOnly(array $statuses): bool
    {
        return in_array('completed', $statuses) && !in_array('not-completed', $statuses);
    }

    private function hasNotCompletedOnly(array $statuses): bool
    {
        return in_array('not-completed', $statuses) && !in_array('completed', $statuses);
    }

    private function filterCompleted(Builder $query, $user): void
    {
        $query->whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->whereNotNull('user_quiz.completed_at');
        });
    }

    private function filterNotCompleted(Builder $query, $user): void
    {
        $query->whereDoesntHave('users', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->whereNotNull('user_quiz.completed_at');
        });
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

    public function scopeFilterBySearch(Builder $query, $search): Builder
    {
        return $query->where('title', 'LIKE', '%' . $search . '%');
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
