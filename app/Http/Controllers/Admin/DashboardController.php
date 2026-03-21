<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const RANGES = [7, 30, 90];

    public function __invoke(Request $request): View
    {
        $days = in_array($request->integer('days'), self::RANGES, true) ? $request->integer('days') : 30;

        $end = CarbonImmutable::now()->endOfDay();
        $start = $end->subDays($days - 1)->startOfDay();
        $previousStart = $start->subDays($days);

        $current = $this->periodTotals($start, $end);
        $previous = $this->periodTotals($previousStart, $start->subSecond());

        return view('admin.dashboard', [
            'days' => $days,
            'ranges' => self::RANGES,
            'kpis' => [
                'revenue' => ['value' => $current['revenue'], 'delta' => $this->delta($current['revenue'], $previous['revenue'])],
                'orders' => ['value' => $current['orders'], 'delta' => $this->delta($current['orders'], $previous['orders'])],
                'average' => ['value' => $current['average'], 'delta' => $this->delta($current['average'], $previous['average'])],
                'customers' => ['value' => $current['customers'], 'delta' => $this->delta($current['customers'], $previous['customers'])],
            ],
            'revenueSeries' => $this->revenueSeries($start, $end),
            'statusBreakdown' => $this->statusBreakdown($start, $end),
            'topProducts' => $this->topProducts($start, $end),
            'lowStock' => Product::lowStock()->orderBy('stock')->limit(6)->get(),
            'lowStockCount' => Product::lowStock()->count(),
            'needsAction' => Order::with('user:id,name')
                ->whereIn('status', [OrderStatus::WaitingForPayment->value, OrderStatus::Processing->value])
                ->oldest()
                ->limit(8)
                ->get(),
        ]);
    }

    /**
     * @return array{revenue: float, orders: int, average: float, customers: int}
     */
    private function periodTotals(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $orders = Order::countingTowardsRevenue()->whereBetween('created_at', [$from, $to]);

        $revenue = (float) (clone $orders)->sum('total_cost');
        $count = (clone $orders)->count();

        return [
            'revenue' => $revenue,
            'orders' => $count,
            'average' => $count ? round($revenue / $count, 2) : 0.0,
            'customers' => User::where('is_admin', false)->whereBetween('created_at', [$from, $to])->count(),
        ];
    }

    private function delta(float|int $current, float|int $previous): ?float
    {
        if ((float) $previous === 0.0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Revenue and order count for every day in the range, zero-filled.
     *
     * @return array{labels: array<int, string>, revenue: array<int, float>, orders: array<int, int>}
     */
    private function revenueSeries(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = Order::countingTowardsRevenue()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('date(created_at) as day, sum(total_cost) as revenue, count(*) as orders')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = ['labels' => [], 'revenue' => [], 'orders' => []];

        foreach (CarbonPeriod::create($from, $to) as $date) {
            $row = $rows->get($date->toDateString());
            $series['labels'][] = $date->format('j M');
            $series['revenue'][] = round((float) ($row->revenue ?? 0), 2);
            $series['orders'][] = (int) ($row->orders ?? 0);
        }

        return $series;
    }

    /**
     * @return array<int, array{status: OrderStatus, count: int}>
     */
    private function statusBreakdown(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $counts = Order::whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status) => ['status' => $status, 'count' => (int) ($counts[$status->value] ?? 0)])
            ->all();
    }

    private function topProducts(CarbonImmutable $from, CarbonImmutable $to)
    {
        $rows = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereIn('orders.status', array_map(fn (OrderStatus $status) => $status->value, OrderStatus::revenue()))
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotNull('order_details.bmuk_no')
            ->groupBy('order_details.bmuk_no')
            ->selectRaw('order_details.bmuk_no, sum(order_details.quantity) as units, sum(order_details.quantity * order_details.unit_price) as revenue')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $products = Product::withTrashed()->whereIn('bmuk_no', $rows->pluck('bmuk_no'))->get()->keyBy('bmuk_no');

        return $rows->map(fn ($row) => [
            'product' => $products->get($row->bmuk_no),
            'units' => (int) $row->units,
            'revenue' => (float) $row->revenue,
        ])->filter(fn ($row) => $row['product'])->values();
    }
}
