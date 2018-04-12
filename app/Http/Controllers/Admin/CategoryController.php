<?php

namespace App\Http\Controllers\Admin;

use App\Brandshop\Services\AgentService;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(AgentService $agentService)
    {
        $b2signCategories = $agentService->getAllCategories();
        $b2signCategories = $b2signCategories->data ?? $b2signCategories;

        $categories = Category::all()->toTree();

        return view('admin.categories.index', compact('categories', 'b2signCategories'));
    }

    public function resetAndSyncFromB2sign(AgentService $agentService)
    {
        $b2signCategories = $agentService->getAllCategories();
        Category::truncate();

        foreach ($this->resolveCategories($b2signCategories) as $category) {
            Category::create($category);
        }

        flash('Success');

        return redirect()->back();
    }

    private function resolveCategories($categories)
    {
        $data = [];
        foreach ($categories as $category) {
            $item = ['title' => $category->name];
            if (!empty($category->categories) && count($category->categories) > 0) {
                $item['children'] = $this->resolveCategories($category->categories);
            }
            $data[] = $item;
        }

        return $data;
    }

    public function save(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|string|unique:categories,title',
        ]);

        $parent = Category::find($request->get('parent'));
        if ($parent) {
            $parent->children()->create($data);
        } else {
            Category::create($data); // Saved as root
        }

        flash('Success');
        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        flash('Deleted');

        return redirect()->back();
    }
}
