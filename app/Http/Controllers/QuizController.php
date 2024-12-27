<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function getQuizzes(Request $request)
    {
        $user = Auth::user();

        $quizzesQuery = Quiz::with([
            'questions' => function ($query) {
                $query->select('id', 'quiz_id', 'point');
            },
            'level' => function ($query) {
                $query->select('id', 'level', 'icon_color', 'background_color');
            },
            'categories' => function ($query) {
                $query->select('categories.id', 'categories.name');
            }
        ]);

        if ($user) {
            $quizzesQuery->with(['users' => function ($query) use ($user) {
                $query->where('users.id', $user->id)
                      ->select('users.id', 'user_quiz.completed_at', 'user_quiz.total_time', 'user_quiz.user_result');
            }]);

            if ($request->has('completed')) {
                $completed = explode(',', $request->query('completed'));
                $hasCompleted = in_array('completed', $completed);
                $hasNotCompleted = in_array('not-completed', $completed);

                if ($hasCompleted && !$hasNotCompleted) {
                    $quizzesQuery->whereHas('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id)
                              ->whereNotNull('user_quiz.completed_at');
                    });
                } elseif ($hasNotCompleted && !$hasCompleted) {
                    $quizzesQuery->whereDoesntHave('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id)
                              ->whereNotNull('user_quiz.completed_at');
                    });
                }
            }

        } else {
            $quizzesQuery->without('users');
        }

        if ($request->has('levels')) {
            $levels = explode(',', $request->query('levels'));
            $quizzesQuery->whereIn('level_id', $levels);
        }

        if ($request->has('categories')) {
            $categories = explode(',', $request->query('categories'));
            $quizzesQuery->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            });
        }

        if ($request->has('sortBy')) {
            $sortBy = $request->query('sortBy');

            switch ($sortBy) {
                case 'A-Z':
                    $quizzesQuery->orderBy('title', 'asc');
                    break;

                case 'Z-A':
                    $quizzesQuery->orderBy('title', 'desc');
                    break;

                case 'Most popular':
                    $quizzesQuery->orderBy('total_filled', 'desc');
                    break;

                case 'Newest':
                    $quizzesQuery->orderBy('created_at', 'desc');
                    break;

                case 'Oldest':
                    $quizzesQuery->orderBy('created_at', 'asc');
                    break;

            }
        }

        $quizzes = $quizzesQuery->simplePaginate(12);
        return response()->json($quizzes);
    }
}
