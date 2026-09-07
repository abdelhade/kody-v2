<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\Unit;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService
    ) {}

    /**
     * Display a listing of the items.
     */
    public function index(Request $request): Response
    {
        $search = trim($request->input('search', ''));
        $groupId = $request->input('group');

        $query = Item::with(['units.unit', 'group'])
            ->where('isdeleted', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iname', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($groupId)) {
            $query->where('group1', $groupId);
        }

        $items = $query->latest('id')->paginate(15)->withQueryString();

        $groups = ItemGroup::where('isdeleted', 0)->orderBy('gname')->get(['id', 'gname']);
        $units  = Unit::where('isdeleted', 0)->orderBy('uname')->get(['id', 'uname']);

        $stats = [
            'total_items' => Item::where('isdeleted', 0)->count(),
            'total_stock' => (float) Item::where('isdeleted', 0)->sum('itmqty'),
            'groups_count' => $groups->count(),
        ];

        return Inertia::render('Items/Index', [
            'items'   => $items,
            'groups'  => $groups,
            'units'   => $units,
            'filters' => [
                'search' => $search,
                'group'  => $groupId,
            ],
            'stats'   => $stats,
        ]);
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iname'        => 'required|string|max:200',
            'name2'        => 'nullable|string|max:200',
            'code'         => 'nullable|string|max:50',
            'barcode'      => 'nullable|string|max:50',
            'group1'       => 'nullable|integer',
            'info'         => 'nullable|string|max:250',
            'units'        => 'required|array|min:1',
            'units.*.unit_id'      => 'required|integer',
            'units.*.u_val'        => 'required|numeric|min:0.001',
            'units.*.unit_barcode' => 'nullable|string|max:50',
            'units.*.cost_price'   => 'nullable|numeric|min:0',
            'units.*.price1'       => 'required|numeric|min:0',
            'units.*.price2'       => 'nullable|numeric|min:0',
            'units.*.price3'       => 'nullable|numeric|min:0',
        ], [
            'iname.required' => 'اسم الصنف مطلوب.',
            'units.required' => 'يجب إضافة وحدة واحدة على الأقل للصنف.',
            'units.*.unit_id.required' => 'يرجى اختيار الوحدة.',
            'units.*.price1.required'  => 'سعر البيع مطلوب.',
        ]);

        $this->itemService->createItem($validated, auth()->id() ?? 1);

        return redirect()->back()->with('success', 'تمت إضافة الصنف بنجاح!');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        $item->update(['isdeleted' => 1]);
        $item->units()->update(['isdeleted' => 1]);

        return redirect()->back()->with('success', 'تم حذف الصنف بنجاح.');
    }
}
