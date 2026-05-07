<?php
namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Contact;
use App\Models\TimelineEntry;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $upcoming = Meeting::where("user_id", auth()->id())
            ->where("meeting_at", ">=", now())
            ->orderBy("meeting_at")
            ->get();
        $past = Meeting::where("user_id", auth()->id())
            ->where("meeting_at", "<", now())
            ->orderBy("meeting_at", "desc")
            ->take(20)->get();
        return view("meetings.index", compact("upcoming","past"));
    }

    public function create()
    {
        $contacts = Contact::where("user_id", auth()->id())->orderBy("name")->get();
        return view("meetings.create", compact("contacts"));
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            "title"           => "required|string|max:255",
            "contractor_name" => "nullable|string|max:255",
            "contact_id"      => "nullable|exists:contacts,id",
            "meeting_at"      => "required|date",
            "location"        => "nullable|string|max:255",
            "agenda"          => "nullable|string",
            "notes"           => "nullable|string",
            "status"          => "nullable|string|max:50",
        ]);
        $d["user_id"] = auth()->id();
        $meeting = Meeting::create($d);

        TimelineEntry::create([
            "user_id"        => auth()->id(),
            "type"           => "meeting",
            "entry_title"    => "Spotkanie: " . $meeting->title,
            "entry_body"     => $meeting->contractor_name ? "Wykonawca: " . $meeting->contractor_name : null,
            "entry_date"     => $meeting->meeting_at,
            "entryable_type" => Meeting::class,
            "entryable_id"   => $meeting->id,
        ]);

        return redirect()->route("meetings.index")->with("success","Spotkanie zostało zaplanowane.");
    }

    public function edit(Meeting $meeting)
    {
        abort_if($meeting->user_id !== auth()->id(), 403);
        $contacts = Contact::where("user_id", auth()->id())->orderBy("name")->get();
        return view("meetings.create", compact("meeting","contacts"));
    }

    public function update(Request $request, Meeting $meeting)
    {
        abort_if($meeting->user_id !== auth()->id(), 403);
        $d = $request->validate([
            "title"           => "required|string|max:255",
            "contractor_name" => "nullable|string|max:255",
            "contact_id"      => "nullable|exists:contacts,id",
            "meeting_at"      => "required|date",
            "location"        => "nullable|string|max:255",
            "agenda"          => "nullable|string",
            "notes"           => "nullable|string",
            "status"          => "nullable|string|max:50",
        ]);
        $meeting->update($d);
        return redirect()->route("meetings.show", $meeting)->with("success","Spotkanie zaktualizowane.");
    }

    public function show(Meeting $meeting)
    {
        abort_if($meeting->user_id !== auth()->id(), 403);
        return view("meetings.show", compact("meeting"));
    }

    public function destroy(Meeting $meeting)
    {
        abort_if($meeting->user_id !== auth()->id(), 403);
        $meeting->delete();
        return redirect()->route("meetings.index")->with("success","Spotkanie usunięte.");
    }
}