<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Level;
use App\Models\Quiz;
use Tests\TestCase;

class FilterTest extends TestCase
{
    public function test_filter_with_level()
    {
        $level = Level::factory()->create(['level' => 'beginner']);

        Quiz::factory()->create(['level_id' => $level->id]);
        Quiz::factory()->create(['level_id' => $level->id]);

        $response = $this->get(route('quizzes.index', ['levels' => $level->id]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $levelIds = array_map(function ($quiz) {
            return $quiz['level']['id'];
        }, $quizzes);

        $this->assertCount(1, array_unique($levelIds), 'Not all quizzes have the same level.');
    }

    public function test_filter_with_categories()
    {
        $category = Category::factory()->create(['name' => 'Science']);
        $quiz = Quiz::factory()->create(['title' => 'Science Quiz']);

        $quiz->categories()->attach($category->id);

        $quizWithCategories = Quiz::with('categories')->find($quiz->id);

        $this->assertTrue($quizWithCategories->categories->contains($category), 'The quiz does not contain the expected category.');
    }

    public function test_filter_sort_by_title_asc()
    {
        Quiz::factory()->create(['title' => 'Apple Quiz']);
        Quiz::factory()->create(['title' => 'Banana Quiz']);
        Quiz::factory()->create(['title' => 'Carrot Quiz']);

        $response = $this->get(route('quizzes.index', [
            'sortBy' => 'title',
            'direction' => 'asc'
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $titles = array_map(function ($quiz) {
            return $quiz['title'];
        }, $quizzes);

        $sortedTitles = $titles;
        sort($sortedTitles);

        var_dump($titles);
        $this->assertEquals($sortedTitles, $titles, 'The quizzes are not sorted by title in ascending order.');
    }

    public function test_filter_sort_by_title_desc()
    {
        Quiz::factory()->create(['title' => 'Apple Quiz']);
        Quiz::factory()->create(['title' => 'Banana Quiz']);
        Quiz::factory()->create(['title' => 'Carrot Quiz']);

        $response = $this->get(route('quizzes.index', [
            'sortBy' => 'title',
            'direction' => 'desc'
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $titles = array_map(function ($quiz) {
            return $quiz['title'];
        }, $quizzes);

        $sortedTitles = $titles;
        rsort($sortedTitles);

        var_dump($titles);

        $this->assertEquals($sortedTitles, $titles, 'The quizzes are not sorted by title in descending order.');
    }

    public function test_filter_sort_by_total_filled_desc()
    {
        Quiz::factory()->create(['title' => 'Quiz 1', 'total_filled' => 10]);
        Quiz::factory()->create(['title' => 'Quiz 2', 'total_filled' => 50]);
        Quiz::factory()->create(['title' => 'Quiz 3', 'total_filled' => 30]);

        $response = $this->get(route('quizzes.index', [
            'sortBy' => 'total_filled',
            'direction' => 'desc'
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $totalFilled = array_map(function ($quiz) {
            return $quiz['total_filled'];
        }, $quizzes);

        $sortedTotalFilled = $totalFilled;
        rsort($sortedTotalFilled);

        $this->assertEquals($sortedTotalFilled, $totalFilled, 'The quizzes are not sorted by total_filled in descending order.');
    }

    public function test_filter_sort_by_created_at_asc()
    {
        Quiz::factory()->create(['title' => 'Quiz 1', 'created_at' => now()->subDays(3)]);
        Quiz::factory()->create(['title' => 'Quiz 2', 'created_at' => now()->subDays(2)]);
        Quiz::factory()->create(['title' => 'Quiz 3', 'created_at' => now()->subDays(1)]);

        $response = $this->get(route('quizzes.index', [
            'sortBy' => 'created_at',
            'direction' => 'asc'
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $createdAt = array_map(function ($quiz) {
            return $quiz['created_at'];
        }, $quizzes);

        $sortedCreatedAt = $createdAt;
        sort($sortedCreatedAt);

        $this->assertEquals($sortedCreatedAt, $createdAt, 'The quizzes are not sorted by created_at in ascending order.');
    }


    public function test_filter_sort_by_created_at_desc()
    {
        Quiz::factory()->create(['title' => 'Quiz 1', 'created_at' => now()->subDays(3)]);
        Quiz::factory()->create(['title' => 'Quiz 2', 'created_at' => now()->subDays(2)]);
        Quiz::factory()->create(['title' => 'Quiz 3', 'created_at' => now()->subDays(1)]);

        $response = $this->get(route('quizzes.index', [
            'sortBy' => 'created_at',
            'direction' => 'desc'
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $createdAt = array_map(function ($quiz) {
            return $quiz['created_at'];
        }, $quizzes);

        $sortedCreatedAt = $createdAt;
        rsort($sortedCreatedAt);

        $this->assertEquals($sortedCreatedAt, $createdAt, 'The quizzes are not sorted by created_at in descending order.');
    }

    public function test_filter_with_level_and_categories_and_sorting()
    {
        $level = Level::factory()->create(['level' => 'beginner']);

        $category1 = Category::factory()->create(['name' => 'Science']);
        $category2 = Category::factory()->create(['name' => 'Math']);

        $quiz1 = Quiz::factory()->create(['title' => 'Apple Quiz', 'level_id' => $level->id]);
        $quiz1->categories()->attach($category1->id);

        $quiz2 = Quiz::factory()->create(['title' => 'Banana Quiz', 'level_id' => $level->id]);
        $quiz2->categories()->attach($category2->id);

        $quiz3 = Quiz::factory()->create(['title' => 'Carrot Quiz', 'level_id' => $level->id]);
        $quiz3->categories()->attach($category1->id);
        $quiz3->categories()->attach($category2->id);

        $response = $this->get(route('quizzes.index', [
            'levels' => $level->id,
            'categories' => $category1->id,
            'sortBy' => 'title',
            'direction' => 'asc',
        ]));

        $response->assertStatus(200);

        $quizzes = $response->json()['data'];

        $levelIds = array_map(function ($quiz) {
            return $quiz['level']['id'];
        }, $quizzes);

        $categoryIds = array_map(function ($quiz) {
            return collect($quiz['categories'])->pluck('id')->toArray();
        }, $quizzes);

        foreach ($levelIds as $levelId) {
            $this->assertEquals($level->id, $levelId, 'Quiz does not have the correct level.');
        }

        foreach ($categoryIds as $categories) {
            $this->assertContains($category1->id, $categories, 'Quiz does not have the correct category.');
        }

        $titles = array_map(function ($quiz) {
            return $quiz['title'];
        }, $quizzes);

        $sortedTitles = $titles;
        sort($sortedTitles);

        $this->assertEquals($sortedTitles, $titles, 'The quizzes are not sorted by title in ascending order.');
    }
}
