<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');
        $q = $request->query('q');

        // List all categories for the filter UI
        $categories = Category::orderBy('name')->get();

        // Base products query, eager load category
        $query = Product::with('category')->orderBy('created_at', 'desc');

        // If a category slug is present, filter by it
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                // unknown category -> no results
                $query->whereRaw('1 = 0');
            }
        }

        // Search by product name or category name
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhereHas('category', function ($qc) use ($q) {
                       $qc->where('name', 'like', "%{$q}%");
                   });
            });
        }

        // Paginate and preserve query string for pagination links
        $products = $query->paginate(12)->withQueryString();

        return view('store.index', compact('products', 'categories', 'categorySlug', 'q'));
    }

public function show($slug)
{
    $product = Product::where('slug', $slug)->firstOrFail();
    return view('store.product', compact('product'));
}

public function pay(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:10000',
        'description' => 'nullable|string|max:255',
    ]);

    // Create payment via PaymentController
    $paymentController = new PaymentController(app('MidtransService'));
    return $paymentController->createPayment($request);
}

}
