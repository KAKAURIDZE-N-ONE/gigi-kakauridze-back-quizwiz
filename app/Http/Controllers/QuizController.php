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
            'categories' => function ($query) {
                $query->select('categories.id', 'categories.name');
            }]);

        if ($user) {
            $quizzesQuery->with(['users' => function ($query) use ($user) {
                $query->where('users.id', $user->id) // Filter for the logged-in user
                      ->select('users.id', 'user_quiz.completed_at', 'user_quiz.total_time', 'user_quiz.user_result'); // Include pivot data
            }]);

        } else {
            $quizzesQuery->without('users');
        }

        $quizzes = $quizzesQuery->simplePaginate(12);
        return response()->json($quizzes);
    }
}
