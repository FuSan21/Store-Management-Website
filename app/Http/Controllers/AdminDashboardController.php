<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Models\Product as ModelsProduct;

class AdminDashboardController extends Controller
{
    public static function index()
    {
        $pageName = 'Admin Dashboard';
        $orders = ModelsOrder::orderBy('created_at', 'desc')->with('orderDetails')->take(5)->get()->toArray();
        $temp_orders = ModelsOrder::with('orderDetails')->get()->toArray();
        $order_costs = [];
        foreach ($orders as $order) {
            $tempOrderCost = 0;
            foreach ($order['order_details'] as $order_detail) {
                $product = ModelsProduct::where('bmuk_no', $order_detail['bmuk_no'])->first()->toArray();
                $tempOrderCost += $product['price'] * $order_detail['quantity'];
            }
            $order_costs[$order['id']] = $tempOrderCost;
        }
        $orderCount = ModelsOrder::count();
        $totalOrderCost = 0;
        foreach ($temp_orders as $order) {
            $tempOrderCost = 0;
            foreach ($order['order_details'] as $order_detail) {
                $product = ModelsProduct::where('bmuk_no', $order_detail['bmuk_no'])->first()->toArray();
                $tempOrderCost += $product['price'] * $order_detail['quantity'];
            }
            $totalOrderCost += $tempOrderCost;
        }
        $data = compact('orders', 'pageName', 'orderCount', 'order_costs', 'totalOrderCost');
        return view('admindash')->with($data);
    }
}
