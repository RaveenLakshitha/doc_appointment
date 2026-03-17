<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionList;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    public function index()
    {
        $groupedOptions = OptionList::orderBy('type')
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('admin.dropdowns.index', compact('groupedOptions'));
    }

    // Add new option (to existing type)
    public function store(Request $request)
    {
        $request->validate([
            'type'   => 'required|string|max:100|exists:option_lists,type',
            'name'   => 'required|string|max:255|unique:option_lists,name,NULL,id,type,' . $request->type,
            'order'  => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);

        OptionList::create($request->only('type', 'name', 'order', 'status'));

        return redirect()->route('dropdowns.index')
            ->with('success', __('file.option_created_successfully'));
    }

    // Edit existing option
    public function update(Request $request, OptionList $option)
    {
        $request->validate([
            'type'   => 'required|string|max:100',
            'name'   => 'required|string|max:255|unique:option_lists,name,' . $option->id . ',id,type,' . $request->type,
            'order'  => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);

        $option->update($request->only('type', 'name', 'order', 'status'));

        return redirect()->route('dropdowns.index')
            ->with('success', __('file.option_updated_successfully'));
    }

    // Delete single option
    public function destroy(OptionList $option)
    {
        if ($option->inUse()) {
            return redirect()->route('dropdowns.index')
                ->with('error', __('file.option_in_use'));
        }

        // Clear references in soft-deleted or inactive records to avoid integrity constraint violations
        $option->cleanupReferences();

        $option->delete();

        return redirect()->route('dropdowns.index')
            ->with('success', __('file.option_deleted_successfully'));
    }
}