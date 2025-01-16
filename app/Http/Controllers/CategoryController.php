<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories(Request $request)
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function getCategoriesLength(Request $request)
    {
        $categoriesCount = Category::count();
        return response()->json(['length' => $categoriesCount]);
    }
}
