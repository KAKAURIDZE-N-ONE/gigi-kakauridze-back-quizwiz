<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;

class QuizController extends Controller
{
    public function getQuizzesQuantity(Request $request)
    {
        $quizzesQuantity = Quiz::count();
        return response()->json(['length' => $quizzesQuantity]);
    }

    public function getQuiz(Quiz $quiz, Request $request)
    {
        $user = Auth::user();

        $quiz->load([
            'questions:id,quiz_id,point,question',
            'level:id,level,icon_color,background_color',
            'categories:id,name',
        ]);

        if ($user) {
            $quiz->load([
                'users' => fn ($query) => $query
                    ->where('users.id', $user->id)
                    ->select('users.id')
                    ->withPivot('completed_at', 'total_time', 'user_result'),
            ]);
        }

        $quiz->load('questions.answers:id,question_id,answer,is_correct');


        return response()->json(new QuizResource($quiz));
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
        ->when($request->has('search'), function ($query) use ($request) {
            $query->filterBySearch($request->query('search'));
        })
        ->when($request->has('sortBy') && $request->has('direction'), function ($query) use ($request) {
            $field = $request->query('sortBy');
            $direction = $request->query('direction');
            $query->applySorting($field, $direction);
        })
        ->when($request->has('except'), function ($query) use ($request) {
            $query->where('id', '!=', $request->query('except'));
        })
        ;

        $limit = $request->query('limit', 12);

        $quizzes = $quizzesQuery->simplePaginate($limit);
        return QuizResource::collection($quizzes);
    }

    public function submitQuiz(Request $request, Quiz $quiz)
    {
        $user = Auth::user();

        $questionsWithAnswers = $quiz->questions()->with('answers')->get();

        $selectedPairs = $request->input('selectedAnswers');

        [$totalPoints, $totalResult] = $this->calculateQuizResults($selectedPairs, $questionsWithAnswers);

        if ($user) {
            $quiz->users()->attach($user->id, [
                'completed_at' => Carbon::now(),
                'total_time' => $quiz->duration - $request->input('timer'),
                'user_result' => $totalResult
            ]);
        }

        $quiz->increment('total_filled');

        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully!',
            'uploaded_quiz' => ['correct_quantity' => $totalPoints, 'total_time' => $quiz->duration - $request->input('timer')]
        ]);
    }

    private function calculateQuizResults($selectedPairs, $questionsWithAnswers)
    {
        $totalResult = 0;
        $totalPoints = 0;
        foreach ($selectedPairs as $selectedPair) {
            $selectedQuestionId = $selectedPair['question_id'];
            $selectedAnswersIds = $selectedPair['answer_ids'];

            $question = $questionsWithAnswers->firstWhere('id', $selectedQuestionId);

            if ($question) {

                $answers = $question->answers;
                $correctAnswerIds = $answers->where('is_correct', true)->pluck('id')->toArray();

                if (empty(array_diff($selectedAnswersIds, $correctAnswerIds)) && empty(array_diff($correctAnswerIds, $selectedAnswersIds))) {
                    $totalPoints += $question->point;
                    $totalResult++;
                }
            }
        }

        return [$totalResult, $totalPoints];
    }
}
