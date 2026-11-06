<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\FilterOption;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class FilterController extends Controller
{
    // List all filters
    public function index()
    {
        return view('purchase.filters.index');
    }

    // Datatable for filters
    public function dataTable()
    {
        $query = Filter::with('options');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Filter $filter) {
                $btn = '<a href="' . route('filters.edit', ['filter' => $filter->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a role="button" data-id="' . $filter->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->addColumn('options', function(Filter $filter){
                return $filter->options ? $filter->options->pluck('value')->implode(', ') : '-';
            })
            ->rawColumns(['action','options'])
            ->toJson();
    }

    // Show create form
    public function create()
    {
        return view('purchase.filters.create');
    }

    // Store filter and options
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:filters,name',
            'type' => 'required|in:text,number,select,radio,checkbox',
            'options' => 'nullable|array'
        ]);

        $filter = Filter::create([
            'name' => $request->name,
            'type' => $request->type
        ]);

        if (in_array($request->type, ['select','radio','checkbox']) && $request->options) {
            foreach ($request->options as $option) {
                FilterOption::create([
                    'filter_id' => $filter->id,
                    'value' => $option
                ]);
            }
        }

        return redirect()->route('filters.index')->with('success', 'Filter created successfully!');
    }

    // Show edit form
    public function edit(Filter $filter)
    {
        $filter->load('options');
        return view('purchase.filters.edit', compact('filter'));
    }

    // Update filter and options
    public function update(Request $request, Filter $filter)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:filters,name,' . $filter->id,
            'type' => 'required|in:text,number,select,radio,checkbox',
            'options' => 'nullable|array'
        ]);

        $filter->update([
            'name' => $request->name,
            'type' => $request->type
        ]);

        if (in_array($request->type, ['select','radio','checkbox'])) {
            $existingOptionIds = $request->input('option_ids', []); // send IDs from form
            $optionValues = $request->input('options', []);

            // Delete options that are not in form
            $filter->options()->whereNotIn('id', $existingOptionIds)->delete();

            // Update existing options
            foreach ($existingOptionIds as $index => $id) {
                $option = $filter->options()->find($id);
                if ($option) {
                    $option->update(['value' => $optionValues[$index]]);
                }
            }

            // Add new options
            foreach ($optionValues as $index => $value) {
                if (!isset($existingOptionIds[$index])) {
                    FilterOption::create([
                        'filter_id' => $filter->id,
                        'value' => $value
                    ]);
                }
            }
        } else {
            // For text/number type, remove any old options
            $filter->options()->delete();
        }

        return redirect()->route('filters.index')->with('success', 'Filter updated successfully!');
    }

    // Delete filter
    public function destroy(Filter $filter)
    {
        try {
            // Check if filter assigned to any product
            $related = Product::whereHas('filterValues', function ($q) use ($filter) {
                $q->where('filter_id', $filter->id);
            })->first();

            if ($related) {
                return response()->json([
                    'success' => false,
                    'message' => 'This filter is assigned to a product. Cannot delete.'
                ], Response::HTTP_OK);
            }

            $filter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Filter deleted successfully.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filter not found: ' . $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
