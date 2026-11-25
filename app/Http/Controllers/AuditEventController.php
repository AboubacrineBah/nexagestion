<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditEventController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AuditEvent::class);

        $events = AuditEvent::with('user')
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(20);

        return view('audit-events.index', compact('events'));
    }

    public function show(AuditEvent $event): View
    {
        abort_unless($event->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $event);

        return view('audit-events.show', compact('event'));
    }
}
