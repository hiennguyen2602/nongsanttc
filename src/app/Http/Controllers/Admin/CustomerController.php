<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->when($request->q, function ($query) use ($request) {
                $q = $request->q;
                $phone = Customer::normalizePhone($q);
                $query->where(function ($builder) use ($q, $phone) {
                    $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$phone}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => fn ($query) => $query->latest()]);

        return view('admin.customers.show', compact('customer'));
    }
}
