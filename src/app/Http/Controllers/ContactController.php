<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'role'    => 'nullable|string|max:100',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ]);
        $validated['user_id'] = auth()->id();
        Contact::create($validated);
        return redirect()->route('contacts.index')->with('success', 'Kontakt został dodany.');
    }

    public function edit(Contact $contact)
    {
        abort_if($contact->user_id !== auth()->id(), 403);
        return view('contacts.create', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        abort_if($contact->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'role'    => 'nullable|string|max:100',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ]);
        $contact->update($validated);
        return redirect()->route('contacts.index')->with('success', 'Kontakt został zaktualizowany.');
    }

    public function destroy(Contact $contact)
    {
        abort_if($contact->user_id !== auth()->id(), 403);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontakt został usunięty.');
    }
}
