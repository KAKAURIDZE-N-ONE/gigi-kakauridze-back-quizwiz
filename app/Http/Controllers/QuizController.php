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


        $quizzes = $quizzesQuery->simplePaginate(12);
        return response()->json($quizzes);
    }
}
