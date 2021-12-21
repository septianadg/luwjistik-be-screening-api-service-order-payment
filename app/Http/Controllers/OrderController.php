<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $orders = Order::query();

        $orders->when($userId, function($query) use ($userId) {
            return $query->where('user_id', '=', $userId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $orders->get()
        ]);
    }

    public function getbyorderid(Request $request)
    {
        //$orderId = $request->input('order_id');

        $orderId = $request->query('order_id');

        $orders = Order::with('orders_tracking')
        ->find($orderId);

        if (!$orders) {
            return response()->json([
                'status' => 'error',
                'message' => 'order not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);

    }

    public function create(Request $request)
    {
        $user = $request->input('user');
        $service = $request->input('service');

        if (!$user || !$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'user and service must filled'
            ]);
        }

        $order = Order::create([
            'user_id' => $user['id'],
            'service_id' => $service['id']
        ]);

        $midtransSnapUrl = 'https://snapurlpayment.midtransss.com/idhashing';

        $order->snap_url_payment = $midtransSnapUrl;

        $order->metadata_snapshot_service = [
            'service_id' => $service['id'],
            'service_price' => $service['price'],
            'service_name' => $service['name'],
            'service_weight_unit' => $service['weight_unit'],
            'service_dimension_unit' => $service['dimension_unit'],
            'service_max_weight' => $service['max_weight'],
            'service_max_dimension' => $service['max_dimension'],
            'service_delivery_estimation' => $service['delivery_estimation'],
            'order_weight' => $service['order_weight'],
            'order_dimension' => $service['order_dimension'],
            'order_pay' => $service['order_pay'],
            'order_type' => $service['order_type']
        ];

        $order->metadata_snapshot_customer = [
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_sender_name' => $user['sender_name'],
            'user_sender_contact' => $user['sender_contact'],
            'user_receiver_name' => $user['receiver_name'],
            'user_receiver_address' => $user['receiver_address'],
            'user_receiver_contact' => $user['receiver_contact']
        ];

        $order->save();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function createtracking(Request $request)
    {
        $rules = [
            'order_id' => 'required|integer',
            'checkpoints' => 'required|in:Out for delivery,Processed at warehouse,Order picked up,Delivered'            
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $orderId = $request->input('order_id');
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'order not found'
            ], 404);
        }

        $ordertracking = OrderTracking::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $ordertracking
        ]);
    }

    public function gettracking(Request $request)
    {
        $ordertracking = OrderTracking::query();

        $order_id = $request->query('order_id');

        $ordertracking->when($order_id, function($query) use ($order_id) {
            return $query->where('order_id', '=', $order_id);
        });

        return response()->json([
            'status' => 'success',
            'data' => $ordertracking->get()
        ]);
    }
}
