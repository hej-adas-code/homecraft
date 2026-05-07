<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('user_id', auth()->id())
            ->latest()
            ->get();
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);
        $validated['user_id'] = auth()->id();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $validated['file_path']  = $path;
            $validated['file_name']  = $file->getClientOriginalName();
            $validated['file_size']  = $file->getSize();
        }
        Document::create($validated);
        return redirect()->route('documents.index')->with('success', 'Dokument został dodany.');
    }

    public function edit(Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);
        return view('documents.create', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }
        $document->update($validated);
        return redirect()->route('documents.index')->with('success', 'Dokument został zaktualizowany.');
    }

    public function destroy(Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokument został usunięty.');
    }
}
