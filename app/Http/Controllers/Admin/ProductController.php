<?php


namespace App\Http\Controllers\Admin;


use App\Brandshop\Services\AgentService;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(AgentService $agentService)
    {
        $products = Product::all();

        $categories = $agentService->getAllCategories();

        return view('admin.products.index', compact('products', 'categories'));
    }
}