<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;

class UnitOfMeasureController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $sort       = $request->get('sort', 'name');
        $direction  = $request->get('direction', 'asc');

        $units = UnitOfMeasure::query()
            ->when($search, fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('abbreviation', 'like', "%{$search}%")
            )
            ->when(in_array($sort, ['name', 'abbreviation']), fn($q) => $q->orderBy($sort, $direction))
            ->active()
            ->paginate(10)
            ->appends($request->query());

        return view('unit-of-measures.index', compact('units', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('unit-of-measures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255|unique:unit_of_measures,name',
            'abbreviation' => 'nullable|string|max:10',
            'is_active'    => 'sometimes|boolean',
        ]);

        UnitOfMeasure::create($request->only(['name', 'abbreviation']) + ['is_active' => true]);

        return redirect()->route('unit-of-measures.index')->with('success', 'Unit of measure created.');
    }

    public function show(UnitOfMeasure $unitOfMeasure)
    {
        return view('unit-of-measures.show', compact('unitOfMeasure'));
    }

    public function edit(UnitOfMeasure $unitOfMeasure)
    {
        return view('unit-of-measures.edit', compact('unitOfMeasure'));
    }

    public function update(Request $request, UnitOfMeasure $unitOfMeasure)
    {
        $request->validate([
            'name'         => 'required|string|max:255|unique:unit_of_measures,name,' . $unitOfMeasure->id,
            'abbreviation' => 'nullable|string|max:10',
            'is_active'    => 'sometimes|boolean',
        ]);

        $unitOfMeasure->update($request->only(['name', 'abbreviation', 'is_active']));

        return redirect()->route('unit-of-measures.index')->with('success', 'Unit of measure updated.');
    }

    public function destroy(UnitOfMeasure $unitOfMeasure)
    {
        $unitOfMeasure->update(['is_active' => false]);
        $unitOfMeasure->delete();

        return back()->with('success', 'Unit of measure deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:unit_of_measures,id',
        ]);

        UnitOfMeasure::whereIn('id', $request->ids)
            ->update(['is_active' => false]);

        UnitOfMeasure::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Selected units deleted.');
    }
}