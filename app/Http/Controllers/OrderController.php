<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_id = request("customer_id");
        $status = request("status");

        $query = Order::query()->with('items');
        if ($customer_id) {
            $query = $query->where("customer_id", "=", $customer_id);
        }

        if ($status) {
            $query = $query->where("status", "=", $status);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $query->orderBy('id', 'desc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // $user = auth('api')->user();
        $items = $request->items;

        $order = Order::create([
            'customer_id' => 1,
            'items' => $items,
            'status' => 'pending',
            'order_date' => Carbon::now(),
            'total_amount' => 0,
        ]);
        $total_amount = 0;

        try {
            foreach ($items as $item) {
                $validator = Validator::make($item, [
                    'medicine_id' => ['required', 'numeric'],
                    'quantity' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Bad request',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $medicine = Medicine::find($item['medicine_id']);

                if (!$medicine) {
                    $validator->errors()->add('medicine_id', 'Not found');
                    return response()->json([
                        'message' => 'Bad request',
                        'errors' => $validator->errors()
                    ], 422);
                }

                if (!$medicine->quantity < $item['quantity']) {
                    $validator->errors()->add('medicine_id', 'No enough quantity');
                    return response()->json([
                        'message' => 'Bad request',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $sale_price = $item['quantity'] * $medicine->price;
                OrderItem::create([
                    'medicine_id' => $item["medicine_id"],
                    'order_id' => $order->id,
                    'quantity' => $item["quantity"],
                    'sale_price' => $sale_price,
                ]);

                $total_amount = $total_amount + $sale_price;
            }
        } catch (\Throwable $ex) {
            Order::destroy($order->id);
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $ex->getMessage()
            ], 500);
        }

        $order->total_amount = $total_amount;
        $order->save();
        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with('items')->where('id', $id)->first();
        if (!$order) {
            return response()->json([], 404);
        }

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'in:pending,delivering,done',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::with('items')->where('id', $id)->first();
        if (!$order) {
            return response()->json([], 404);
        }

        $order->status = $request->status;
        $order->save();

        foreach ($order->items as $item) {
            if ($order->status == 'delivering') {
                $medicine = Medicine::find($item->id);
                $medicine->quantity = $medicine->quantity - $item->quantity;
                $medicine->save();
            }
        }

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([], 404);
        }

        Order::destroy($id);
        return response()->json([], 204);
    }
}
