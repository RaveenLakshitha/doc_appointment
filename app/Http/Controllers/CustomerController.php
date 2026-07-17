<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customers.index',  ['only' => ['index', 'show', 'datatable', 'filters']]);
        $this->middleware('permission:customers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customers.edit',   ['only' => ['edit', 'update']]);
        $this->middleware('permission:customers.delete', ['only' => ['destroy', 'bulkDelete']]);
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        if (!Auth::user()->can('customers.index')) {
            return redirect()->route('home')->with('error', __('file.unauthorized'));
        }

        return view('customers.index');
    }

    // ── DataTable ─────────────────────────────────────────────────────────────

    public function datatable(Request $request)
    {
        $query = Customer::query()
            ->whereNull('customers.deleted_at');

        return DataTables::of($query)
            ->addColumn('full_name', fn($r) => trim($r->first_name . ' ' . $r->last_name))
            ->addColumn('action', function ($row) {
                $edit_url   = Auth::user()->can('customers.edit')   ? route('customers.edit', $row)    : '';
                $delete_url = Auth::user()->can('customers.delete') ? route('customers.destroy', $row) : '';
                return compact('edit_url', 'delete_url');
            })
            ->addColumn('delete_url', fn($r) =>
                Auth::user()->can('customers.delete') ? route('customers.destroy', $r) : null
            )
            ->editColumn('active', fn($r) => (bool) $r->active)
            ->make(true);
    }

    // ── Filters ───────────────────────────────────────────────────────────────

    public function filters()
    {
        return response()->json([
            'statuses' => ['active', 'inactive', 'lead'],
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        if (!Auth::user()->can('customers.create')) {
            return redirect()->route('home')->with('error', __('file.unauthorized'));
        }
        return view('customers.create');
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        if (!Auth::user()->can('customers.create')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:50',

            'address'            => 'nullable|string|max:500',
            'city'               => 'nullable|string|max:100',
            'country'            => 'nullable|string|max:100',
            'gender'             => 'nullable|in:male,female,other',
            'date_of_birth'      => 'nullable|date',
            'status'             => 'required|in:active,inactive,lead',

            'notes'              => 'nullable|string',
            'preferred_language' => 'nullable|string|max:50',
            'active'             => 'boolean',
        ]);

        // Auto-generate code
        $last = Customer::withTrashed()->latest('id')->first();
        $next = $last ? ((int) substr($last->code ?? 'CST-000', 4)) + 1 : 1;

        $customer = Customer::create(array_merge($validated, [
            'code'       => sprintf('CST-%03d', $next),
            'active'     => $request->boolean('active', true),
            'created_by' => Auth::id(),
        ]));

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'full_name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email
            ],
            'message' => __('file.customer_created_successfully')
        ]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Customer $customer)
    {
        if (!Auth::user()->can('customers.index')) {
            return redirect()->route('home')->with('error', __('file.unauthorized'));
        }
        return view('customers.show', compact('customer'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Customer $customer)
    {
        if (!Auth::user()->can('customers.edit')) {
            return redirect()->route('home')->with('error', __('file.unauthorized'));
        }
        return view('customers.edit', compact('customer'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, Customer $customer)
    {
        if (!Auth::user()->can('customers.edit')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:50',

            'address'            => 'nullable|string|max:500',
            'city'               => 'nullable|string|max:100',
            'country'            => 'nullable|string|max:100',
            'gender'             => 'nullable|in:male,female,other',
            'date_of_birth'      => 'nullable|date',
            'status'             => 'required|in:active,inactive,lead',

            'notes'              => 'nullable|string',
            'preferred_language' => 'nullable|string|max:50',
            'active'             => 'boolean',
        ]);

        $customer->update(array_merge($validated, [
            'active' => $request->boolean('active', $customer->active),
        ]));

        return response()->json(['success' => true, 'message' => __('file.customer_updated_successfully')]);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Customer $customer)
    {
        if (!Auth::user()->can('customers.delete')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $customer->delete();

        return response()->json(['success' => true, 'message' => __('file.customer_deleted_successfully')]);
    }

    // ── Bulk Delete ───────────────────────────────────────────────────────────

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('customers.delete')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $request->validate(['ids' => 'required|string']);
        $ids = array_filter(explode(',', $request->ids));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_items_selected')], 422);
        }

        Customer::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => __('file.selected_customers_deleted')]);
    }
    // ── Search (Select2) ──────────────────────────────────────────────────────
    public function search(Request $request)
    {
        $term = trim($request->get('q', '') ?? '');

        $query = Customer::query()
            ->select(['id', 'first_name', 'last_name', 'code', 'phone', 'email'])
            ->whereNull('deleted_at')
            ->when($term, function ($q) use ($term) {
                $q->where(function ($sq) use ($term) {
                    $sq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$term%"])
                        ->orWhere('code', 'LIKE', "%$term%")
                        ->orWhere('phone', 'LIKE', "%$term%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name');

        $customers = $query->paginate(15);

        $results = $customers->getCollection()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->first_name . ' ' . $customer->last_name . ' (' . ($customer->code ?? 'N/A') . ')',
                'code' => $customer->code,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'full_name' => $customer->first_name . ' ' . $customer->last_name
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $customers->hasMorePages()
            ]
        ]);
    }
}
