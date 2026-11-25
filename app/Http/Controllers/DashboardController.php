<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\SalesDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $companyId = Auth::user()->company_id;

        return view('dashboard', [
            'clientCount' => Client::where('company_id', $companyId)->count(),
            'prospectCount' => Client::where('company_id', $companyId)->where('type', 'prospect')->count(),
            'quoteCount' => SalesDocument::where('company_id', $companyId)->where('type', 'quote')->whereIn('status', ['draft', 'sent'])->count(),
            'invoiceCount' => SalesDocument::where('company_id', $companyId)->where('type', 'invoice')->count(),
            'recentDocuments' => SalesDocument::with('client')->where('company_id', $companyId)->latest('issued_at')->limit(6)->get(),
            'received' => Payment::where('company_id', $companyId)->sum('amount'),
        ]);
    }
}
