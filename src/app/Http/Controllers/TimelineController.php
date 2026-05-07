<?php

namespace App\Http\Controllers;

use App\Models\TimelineEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $entries = TimelineEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->take(50)
            ->get();

        return view('timeline.index', compact('entries'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'type'        => 'required|in:note,photo',
            'entry_title' => 'required|string|max:255',
            'entry_body'  => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'entry_date'  => 'nullable|date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('timeline', 'public');
        }

        TimelineEntry::create([
            'user_id'     => $user->id,
            'type'        => $request->type,
            'entry_title' => $request->entry_title,
            'entry_body'  => $request->entry_body,
            'image_path'  => $imagePath,
            'entry_date'  => $request->entry_date ?? now(),
        ]);

        return redirect()->route('timeline')->with('success', 'Wpis dodany.');
    }

    public function destroy(TimelineEntry $timelineEntry)
    {
        $user = auth()->user();

        abort_unless($timelineEntry->user_id === $user->id, 403);

        if (!in_array($timelineEntry->type, ['note', 'photo'])) {
            return back()->with('error', 'Nie można usunąć automatycznego wpisu.');
        }

        if ($timelineEntry->image_path) {
            Storage::disk('public')->delete($timelineEntry->image_path);
        }

        $timelineEntry->delete();

        return redirect()->route('timeline')->with('success', 'Wpis usunięty.');
    }
}
