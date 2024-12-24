<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'quiz_category', 'quiz_id', 'category_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_quiz');
    }
}
