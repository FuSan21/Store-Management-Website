<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = $this->filtered($request)
            ->withCount('orders')
            ->withSum(['orders as spent' => fn (Builder $query) => $query->countingTowardsRevenue()], 'total_cost')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'filters' => $request->only(['q', 'verified']),
            'stats' => [
                'total' => User::where('is_admin', false)->count(),
                'verified' => User::where('is_admin', false)->whereNotNull('email_verified_at')->count(),
                'new' => User::where('is_admin', false)->where('created_at', '>=', now()->subDays(30))->count(),
            ],
        ]);
    }

    public function show(User $user): View
    {
        $user->load('addresses');

        return view('admin.customers.show', [
            'customer' => $user,
            'orders' => $user->orders()->with('orderDetails')->latest()->paginate(10),
            'spent' => (float) $user->orders()->countingTowardsRevenue()->sum('total_cost'),
            'reviews' => $user->reviews()->with('product')->latest()->limit(5)->get(),
        ]);
    }

    public function verify(User $user): RedirectResponse
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return back()->with('toast', ['type' => 'success', 'message' => "{$user->name} is verified"]);
    }

    public function export(Request $request): StreamedResponse
    {
        $customers = $this->filtered($request)
            ->withCount('orders')
            ->withSum(['orders as spent' => fn (Builder $query) => $query->countingTowardsRevenue()], 'total_cost')
            ->orderBy('id');

        return Csv::download('customers-'.now()->format('Y-m-d').'.csv', [
            'ID', 'Name', 'Email', 'Verified', 'Joined', 'Orders', 'Spent',
        ], function () use ($customers) {
            foreach ($customers->lazy(200) as $customer) {
                yield [
                    $customer->id, $customer->name, $customer->email, $customer->email_verified_at ? 'yes' : 'no',
                    $customer->created_at?->format('Y-m-d'), $customer->orders_count, (float) $customer->spent,
                ];
            }
        });
    }

    private function filtered(Request $request): Builder
    {
        return User::query()
            ->where('is_admin', false)
            ->when(trim((string) $request->input('q')), fn (Builder $query, string $term) => $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$term}%")
                ->orWhereLike('email', "%{$term}%")))
            ->when($request->input('verified') === 'yes', fn (Builder $query) => $query->whereNotNull('email_verified_at'))
            ->when($request->input('verified') === 'no', fn (Builder $query) => $query->whereNull('email_verified_at'));
    }
}
