<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ProductBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductBrandController extends Controller
{
    private function generateBrandId(): string
    {
        $lastBrand = ProductBrand::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;

        if ($lastBrand && $lastBrand->id) {

            $lastNumber = (int) substr(
                $lastBrand->id,
                3
            );

            $number = $lastNumber + 1;

        }

        return 'BRN' . str_pad(
            $number,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    public function index()
    {
        return view('product.brand', [
            'editing' => false,
            'generateId' => $this->generateBrandId(),
        ]);
    }

    public function edit(ProductBrand $brand)
    {
        return view('product.brand', [
            'editing' => true,
            'brand' => $brand,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_brands,name',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use($validated, $request){
            ProductBrand::create([
                'uuid' => Str::uuid(),
                'id' => $this->generateBrandId(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $request->has('status') ? 1 : 0,
                'created_by' => auth()->id(),
            ]);
        });


        return redirect()
            ->back()
            ->with('success', 'Product brand created successfully.');
    }

    public function show()
    {
        $brands = ProductBrand::query()
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'uuid' => $brand->uuid,
                    'name' => $brand->name,
                    'description' => $brand->description,
                    'status' => $brand->status ? 'active' : 'inactive',
                ];
            });
        return response()->json($brands);
    }

    public function update(Request $request, ProductBrand $brand) {
        $validated = $request->validate([

            'name' => '
                required|
                string|
                max:255|
                unique:product_brands,name,' . $brand->id,

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

        $brand->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status') ? 1 : 0,
            'updated_by' => auth()->id()
        ]);

        return redirect()
            ->route('master.brand.index')
            ->with(
                'success',
                'Product brand updated successfully.'
            );

    }

    public function destroy(ProductBrand $brand)
    {
        try {
            DB::transaction(function () use ($brand) {
                if (!$brand) {
                    throw new \Exception(
                        'Product brand not found.'
                    );
                }

                $brand->update([
                    'deleted_by' => auth()->user()->id ?? 'system',
                ]);

                $brand->delete();

            });

            return response()->json([
                'success' => true,
                'message' => 'Product brand deleted successfully.'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);

        }
    }
}
