<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ProductUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductUnitController extends Controller
{
    private function generateUnitId(): string
    {
        $lastUnit = ProductUnit::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;

        if ($lastUnit && $lastUnit->id) {

            $lastNumber = (int) substr(
                $lastUnit->id,
                3
            );

            $number = $lastNumber + 1;

        }

        return 'UNT' . str_pad(
            $number,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    public function index()
    {
        return view('product.unit', [
            'editing' => false,
            'generateId' => $this->generateUnitId(),
        ]);
    }

    public function edit(ProductUnit $unit)
    {
        return view('product.unit', [
            'editing' => true,
            'unit' => $unit,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_units,name',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use($validated, $request){
            ProductUnit::create([
                'uuid' => Str::uuid(),
                'id' => $this->generateUnitId(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $request->has('status') ? 1 : 0,
                'created_by' => auth()->id(),
            ]);
        });


        return redirect()
            ->back()
            ->with('success', 'Product unit created successfully.');
    }

    public function show()
    {
        $units = ProductUnit::query()
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

        return response()->json($units);
    }

    public function update(Request $request, ProductUnit $unit) {
        $validated = $request->validate([

            'name' => '
                required|
                string|
                max:255|
                unique:product_units,name,' . $unit->id,

            'description' => '
                nullable|
                string',

            'status' => '
                nullable'

        ]);

        $unit->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status') ? 1 : 0,
            'updated_by' => auth()->id()
        ]);

        return redirect()
            ->route('master.unit.index')
            ->with(
                'success',
                'Product unit updated successfully.'
            );
    }

    public function destroy(ProductUnit $unit)
    {
        try {
            DB::transaction(function () use ($unit) {
                if (!$unit) {
                    throw new \Exception(
                        'Product unit not found.'
                    );
                }

                $unit->update([
                    'deleted_by' => auth()->user()->id ?? 'system',
                ]);

                $unit->delete();

            });

            return response()->json([
                'success' => true,
                'message' => 'Product unit deleted successfully.'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);

        }
    }
}
