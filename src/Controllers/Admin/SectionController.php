<?php

namespace Azuriom\Plugin\Rules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Rules\Models\Section;
use Azuriom\Plugin\Rules\Requests\SectionRequest;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return view('rules::admin.sections.index', [
            'sections' => Section::orderBy('position')->get(),
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*' => ['integer'],
        ]);

        foreach (array_values($validated['sections']) as $position => $id) {
            Section::whereKey($id)->update(['position' => $position + 1]);
        }

        return response()->json([
            'message' => trans('rules::admin.sections.order_updated'),
        ]);
    }

    public function create()
    {
        return view('rules::admin.sections.create');
    }

    public function store(SectionRequest $request)
    {
        Section::create($request->validated() + [
            'position' => (int) Section::max('position') + 1,
        ]);

        return to_route('rules.admin.sections.index')
            ->with('success', trans('rules::admin.sections.saved'));
    }

    public function edit(Section $section)
    {
        return view('rules::admin.sections.edit', ['section' => $section]);
    }

    public function update(SectionRequest $request, Section $section)
    {
        $section->update($request->validated());

        return to_route('rules.admin.sections.index')
            ->with('success', trans('rules::admin.sections.saved'));
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return to_route('rules.admin.sections.index')
            ->with('success', trans('rules::admin.sections.saved'));
    }
}
