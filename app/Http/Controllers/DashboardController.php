<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'counts' => [
                'items' => Item::where('isdeleted', 0)->count(),
                'clients' => Account::clients()->count(),
                'suppliers' => Account::suppliers()->count(),
                'invoices' => Invoice::where('isdeleted', 0)->count(),
            ],
            'recentItems' => Item::where('isdeleted', 0)
                ->latest('id')
                ->take(5)
                ->get(['id', 'iname', 'barcode', 'price1', 'itmqty']),
            'recentInvoices' => Invoice::where('isdeleted', 0)
                ->latest('id')
                ->take(5)
                ->get(['id', 'pro_date', 'fat_net', 'pro_tybe']),
            'currentTenant' => Tenant::current(),
        ]);
    }
}
