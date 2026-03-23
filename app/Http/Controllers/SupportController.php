<?php

namespace App\Http\Controllers;

use App\Models\TicketSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = TicketSupport::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sujet'         => 'required|string|max:255',
            'categorie'     => 'required|in:bug,question,amelioration,autre',
            'priorite'      => 'required|in:basse,normale,haute,urgente',
            'description'   => 'required|string|min:20',
            'capture_ecran' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('capture_ecran')) {
            $validated['capture_ecran'] = $request->file('capture_ecran')
                ->store('tickets', 'public');
        }

        TicketSupport::create($validated);

        return redirect()->route('support.index')
            ->with('success', 'Votre ticket a été soumis. Nous vous répondrons dans les 24-48h ouvrées.');
    }

    public function show(TicketSupport $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);

        return view('support.show', compact('ticket'));
    }
}
