<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\IdeaCategory;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index()
    {
        $query = Idea::where('user_id', auth()->id())->with('category')->latest();

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        $ideas = $query->get();
        $categories = IdeaCategory::where('user_id', auth()->id())->orderBy('name')->get();

        return view('ideas.index', compact('ideas', 'categories'));
    }

    public function create()
    {
        $categories = IdeaCategory::where('user_id', auth()->id())->orderBy('name')->get();
        return view('ideas.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:idea_categories,id',
            'description' => 'nullable|string',
            'link'        => 'nullable|url|max:500',
            'tags'        => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:5120',
            'image_url'   => 'nullable|url|max:500',
        ]);
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('ideas', 'public');
            unset($validated['image_url']);
        } else {
            unset($validated['image']);
            if (!empty($validated['image_url'])) {
                $validated['image_url'] = $validated['image_url'];
            }
        }

        Idea::create($validated);
        return redirect()->route('ideas.index')->with('success', 'Pomysł został dodany.');
    }

    public function edit(Idea $idea)
    {
        abort_if($idea->user_id !== auth()->id(), 403);
        $categories = IdeaCategory::where('user_id', auth()->id())->orderBy('name')->get();
        return view('ideas.create', compact('idea', 'categories'));
    }

    public function update(Request $request, Idea $idea)
    {
        abort_if($idea->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:idea_categories,id',
            'description' => 'nullable|string',
            'link'        => 'nullable|url|max:500',
            'tags'        => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:5120',
            'image_url'   => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('ideas', 'public');
            $validated['image_url']  = null;
        } else {
            unset($validated['image']);
        }

        $idea->update($validated);
        return redirect()->route('ideas.index')->with('success', 'Pomysł został zaktualizowany.');
    }

    public function destroy(Idea $idea)
    {
        abort_if($idea->user_id !== auth()->id(), 403);
        $idea->delete();
        return redirect()->route('ideas.index')->with('success', 'Pomysł został usunięty.');
    }
}
