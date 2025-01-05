<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class QuizController extends Controller
{
    public function getQuiz(Quiz $quiz, Request $request)
    {
        $user = Auth::user();

        $quizQuery = Quiz::withRelations($user, true)->find($quiz->id);

        return response()->json($quizQuery);

    }

    public function getQuizzes(Request $request)
    {
        $user = Auth::user();

        $quizzesQuery = Quiz::withRelations();

        if ($user) {
            $quizzesQuery->filterByUserCompletion($request, $user);
        } else {
            $quizzesQuery->without('users');
        }

        if ($request->has('levels')) {
            $quizzesQuery->filterByLevels($request->query('levels'));
        }

        if ($request->has('categories')) {
            $quizzesQuery->filterByCategories($request->query('categories'));
        }

        if ($request->has('sortBy') && $request->has('direction')) {
            $field = $request->query('sortBy');
            $direction = $request->query('direction');
            $quizzesQuery->applySorting($field, $direction);
        }

        if ($request->has('except')) {
            $quizzesQuery->where('id', '!=', $request->query('except'));
        }

        $limit = $request->has('limit') ? $request->query('limit') : 12;

        $quizzes = $quizzesQuery->simplePaginate($limit);
        return response()->json($quizzes);
    }
}
