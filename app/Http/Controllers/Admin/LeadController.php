<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramLead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = TelegramLead::with(['user', 'service'])->latest();

        // Optional filtering by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $leads = $query->paginate(20);

        return view('admin.leads.index', compact('leads'));
    }

    public function show(TelegramLead $lead)
    {
        $lead->load(['user', 'service']);
        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, TelegramLead $lead)
    {
        $request->validate([
            'status' => 'required|string|in:new,contacted,in_progress,converted,lost'
        ]);

        $lead->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Lead status updated successfully.');
    }

    public function togglePriority(TelegramLead $lead)
    {
        $lead->update(['is_priority' => !$lead->is_priority]);

        return redirect()->back()->with('success', 'Lead priority toggled successfully.');
    }
}
