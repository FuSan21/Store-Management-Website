<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.addresses.index', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default_shipping')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('account.addresses.form', ['address' => new Address(['label' => 'Home'])]);
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isFirst = $user->addresses()->doesntExist();

        DB::transaction(function () use ($request, $user, $isFirst) {
            $address = $user->addresses()->create([
                ...$request->safe()->except(['is_default_shipping', 'is_default_billing']),
                'is_default_shipping' => $isFirst || $request->boolean('is_default_shipping'),
                'is_default_billing' => $isFirst || $request->boolean('is_default_billing'),
            ]);

            $this->clearOtherDefaults($address);
        });

        return redirect()->route('account.addresses.index')->with('toast', ['type' => 'success', 'message' => 'Address saved']);
    }

    public function edit(Address $address): View
    {
        Gate::authorize('update', $address);

        return view('account.addresses.form', ['address' => $address]);
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        DB::transaction(function () use ($request, $address) {
            $address->update([
                ...$request->safe()->except(['is_default_shipping', 'is_default_billing']),
                'is_default_shipping' => $address->is_default_shipping || $request->boolean('is_default_shipping'),
                'is_default_billing' => $address->is_default_billing || $request->boolean('is_default_billing'),
            ]);

            $this->clearOtherDefaults($address);
        });

        return redirect()->route('account.addresses.index')->with('toast', ['type' => 'success', 'message' => 'Address updated']);
    }

    public function destroy(Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $user = $address->user;
        $address->delete();

        // Keep a default in place when the default address is removed.
        if ($next = $user->addresses()->latest()->first()) {
            $next->update([
                'is_default_shipping' => $next->is_default_shipping || $user->addresses()->where('is_default_shipping', true)->doesntExist(),
                'is_default_billing' => $next->is_default_billing || $user->addresses()->where('is_default_billing', true)->doesntExist(),
            ]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Address removed']);
    }

    private function clearOtherDefaults(Address $address): void
    {
        $others = Address::where('user_id', $address->user_id)->whereKeyNot($address->id);

        if ($address->is_default_shipping) {
            (clone $others)->update(['is_default_shipping' => false]);
        }

        if ($address->is_default_billing) {
            (clone $others)->update(['is_default_billing' => false]);
        }
    }
}
