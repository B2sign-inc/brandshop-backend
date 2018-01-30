<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\ProductCollection;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all()->toTree();
        return new CategoryCollection($categories);
    }

    public function products(Category $category)
    {
        return new ProductCollection($category->products);
    }
}
