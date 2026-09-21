<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\TelegramUser;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Broadcast::latest()->paginate(15);
        return view('admin.broadcast.index', compact('broadcasts'));
    }

    public function create()
    {
        return view('admin.broadcast.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string',
            'keyboard_buttons' => 'nullable|json',
            'scheduled_at' => 'nullable|date|after_or_equal:today',
        ]);

        $keyboardButtons = null;
        if ($request->keyboard_buttons) {
            $keyboardButtons = json_decode($request->keyboard_buttons, true);
        }

        $totalTargets = TelegramUser::count();

        $broadcast = Broadcast::create([
            'name' => $request->name,
            'message' => $request->message,
            'keyboard_buttons' => $keyboardButtons,
            'total_targets' => $totalTargets,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'queued' : 'draft',
        ]);

        return redirect()->route('admin.broadcast.index')->with('success', 'Broadcast created successfully.');
    }

    public function start(Broadcast $broadcast)
    {
        if ($broadcast->status !== 'draft') {
            return redirect()->route('admin.broadcast.index')->with('error', 'Only draft broadcasts can be started manually.');
        }

        \App\Jobs\ProcessBroadcast::dispatch($broadcast);

        return redirect()->route('admin.broadcast.index')->with('success', 'Broadcast started successfully.');
    }

    public function show(Broadcast $broadcast)
    {
        $broadcast->load('messages.user');
        return view('admin.broadcast.show', compact('broadcast'));
    }
}
