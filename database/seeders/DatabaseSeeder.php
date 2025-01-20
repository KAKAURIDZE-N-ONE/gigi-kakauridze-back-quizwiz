<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Level;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FooterTableSeeder::class);

        $categories = Category::factory(12)->create();

        $quizzes = Quiz::factory(26)->create();

        foreach ($quizzes as $quiz) {

            $quiz->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );


            $questions = Question::factory(4)->create([
                'quiz_id' => $quiz->id,
            ]);

            foreach ($questions as $question) {
                $correctAnswersCount = rand(1, 2);
                $correctAnswers = fake()->randomElements(range(0, 3), $correctAnswersCount);

                foreach (range(0, 3) as $i) {
                    Answer::factory()->create([
                        'question_id' => $question->id,
                        'is_correct' => in_array($i, $correctAnswers),
                    ]);
                }
            }
        }
    }

}
