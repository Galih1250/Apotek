<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.index', data: compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.input', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|max:2048',
        ]);

        // default: use image_url if provided
        if ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        // override with uploaded file if exists
        if ($request->hasFile('image_file')) {
            $data['image_url'] = $request->file('image_file')
                                          ->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_file' => 'nullable|image|max:2048',
        ]);

        // keep existing image by default
        if ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        // uploaded file overrides everything
        if ($request->hasFile('image_file')) {
            if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
                Storage::disk('public')->delete($product->image_url);
            }

            $data['image_url'] = $request->file('image_file')
                                          ->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_url) Storage::disk('public')->delete($product->image_url);
        $product->delete();

        return redirect()->route('admin.index')->with('success', 'Produk berhasil dihapus.');
    }
}
