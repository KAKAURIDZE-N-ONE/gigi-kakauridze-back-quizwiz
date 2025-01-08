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

        $quizzesQuery
        ->when($user, function ($query) use ($request, $user) {
            $query->filterByUserCompletion($request, $user);
        }, function ($query) {
            $query->without('users');
        })
        ->when($request->has('levels'), function ($query) use ($request) {
            $query->filterByLevels($request->query('levels'));
        })
        ->when($request->has('categories'), function ($query) use ($request) {
            $query->filterByCategories($request->query('categories'));
        })
        ->when($request->has('sortBy') && $request->has('direction'), function ($query) use ($request) {
            $field = $request->query('sortBy');
            $direction = $request->query('direction');
            $query->applySorting($field, $direction);
        })
        ->when($request->has('except'), function ($query) use ($request) {
            $query->where('id', '!=', $request->query('except'));
        });

        $limit = $request->query('limit', 12);

        $quizzes = $quizzesQuery->simplePaginate($limit);
        return response()->json($quizzes);
    }
}
