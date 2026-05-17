<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ProductCategories;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductCategoriesController extends Controller
{
    private function generateCategoryId(): string
    {
        $lastCategory = ProductCategories::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;

        if ($lastCategory && $lastCategory->id) {

            $lastNumber = (int) substr(
                $lastCategory->id,
                3
            );

            $number = $lastNumber + 1;

        }

        return 'CAT' . str_pad(
            $number,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
    
    public function index()
    {
        return view('product.categories', [
            'generateId' => $this->generateCategoryId(),
            'editing' => false, 
        ]);
    }

    public function edit(ProductCategories $category)
    {
        return view('product.categories', [
            'editing' => true,
            'categories' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use($validated, $request){
            ProductCategories::create([
                'uuid' => Str::uuid(),
                'id' => $this->generateCategoryId(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $request->has('status') ? 1 : 0,
                'created_by' => auth()->id(),
            ]);
        });


        return redirect()
            ->back()
            ->with('success', 'Product category created successfully.');
    }

    public function show()
    {
        $categories = ProductCategories::query()

            ->orderBy('id', 'desc')

            ->get()

            ->map(function ($category) {

                return [
                    'id' => $category->id,
                    'uuid' => $category->uuid,
                    'name' => $category->name,
                    'description' => $category->description,
                    'status' => $category->status ? 'active' : 'inactive',
                ];

            });
        return response()->json($categories);
    }

    public function update(Request $request, ProductCategories $category) {
        $validated = $request->validate([

            'name' => '
                required|
                string|
                max:255|
                unique:product_categories,name,' . $category->id,

            'description' => '
                nullable|
                string',

            'status' => '
                nullable'

        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Data
        |--------------------------------------------------------------------------
        */

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status') ? 1 : 0,
            'updated_by' => auth()->id()
        ]);

        return redirect()
            ->route('master.category.index')
            ->with(
                'success',
                'Product category updated successfully.'
            );

    }

    public function destroy(ProductCategories $category)
    {
        try {
            DB::transaction(function () use ($category) {
                if (!$category) {
                    throw new \Exception(
                        'Product category not found.'
                    );
                }

                $category->update([
                    'deleted_by' => auth()->user()->id ?? 'system',
                ]);

                $category->delete();

            });

            return response()->json([
                'success' => true,
                'message' => 'Product category deleted successfully.'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);

        }
    }
}
