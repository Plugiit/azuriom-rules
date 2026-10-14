<?php

namespace Azuriom\Plugin\PlugiitRules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\PlugiitRules\Models\Section;
use Azuriom\Plugin\PlugiitRules\Requests\SectionRequest;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return view('plugiit-rules::admin.sections.index', [
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
            'message' => trans('plugiit-rules::admin.sections.order_updated'),
        ]);
    }

    public function create()
    {
        return view('plugiit-rules::admin.sections.create');
    }

    public function store(SectionRequest $request)
    {
        Section::create($request->validated() + [
            'position' => (int) Section::max('position') + 1,
        ]);

        return to_route('plugiit-rules.admin.sections.index')
            ->with('success', trans('plugiit-rules::admin.sections.saved'));
    }

    public function edit(Section $section)
    {
        return view('plugiit-rules::admin.sections.edit', ['section' => $section]);
    }

    public function update(SectionRequest $request, Section $section)
    {
        $section->update($request->validated());

        return to_route('plugiit-rules.admin.sections.index')
            ->with('success', trans('plugiit-rules::admin.sections.saved'));
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return to_route('plugiit-rules.admin.sections.index')
            ->with('success', trans('plugiit-rules::admin.sections.saved'));
    }
}
