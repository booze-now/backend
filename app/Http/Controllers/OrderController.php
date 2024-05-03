<?php

namespace App\Http\Controllers;

use App\Models\DrinkUnit;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    /**
     * Fields
     *
     * guest_id: integer
     * recorded_by: ?integer
     * recorded_at: ?datetime
     * made_by: ?integer
     * made_at: ?datetime
     * served_by: ?integer
     * served_at: ?datetime
     * table: ?string
     *
     * Relations
     *
     * guest_id => guest.id
     * recorded_by => employee.id
     * made_by => employee.id
     * served_by => employee.id
     */


    protected static $valid_withs = ['order_details'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $with = [];

        if ($request->with) {
            $with = array_intersect(explode(',', strtolower($request->with)), self::$valid_withs);
        }
        return Order::with($with)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'guest_id' => 'integer|required',
            'recorded_by' => 'integer|sometimes',
            'recorded_at' => 'datetime|sometimes',
            'made_by' => 'integer|sometimes|nullable',
            'made_at' => 'datetime|sometimes|nullable',
            'served_by' => 'integer|sometimes|nullable',
            'served_at' => 'datetime|sometimes|nullable',
            'table' => 'string|sometimes|nullable',
        ]);
        $order = new Order();
        $order->fill($valid)->save();
        return $order;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $with = [];

        if ($request->with) {
            $with = array_intersect(explode(',', strtolower($request->with)), self::$valid_withs);
        }
        return Order::with($with)->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $valid = $request->validate([
            'guest_id' => 'integer|required',
            'recorded_by' => 'integer|sometimes',
            'recorded_at' => 'datetime|sometimes',
            'made_by' => 'integer|sometimes|nullable',
            'made_at' => 'datetime|sometimes|nullable',
            'served_by' => 'integer|sometimes|nullable',
            'served_at' => 'datetime|sometimes|nullable',
            'table' => 'string|sometimes|nullable',
        ]);

        $order->fill($valid)->save();
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        return $order->delete();
    }

    public function scheme()
    {
        $order = Order::firstOrNew();

        // if an existing record was found
        if ($order->exists) {
            $order = $order->attributesToArray();
        } else { // otherwise a new model instance was instantiated
            // get the column names for the table
            $columns = Schema::getColumnListing($order->getTable());

            // create array where column names are keys, and values are null
            $columns = array_fill_keys($columns, null);

            // merge the populated values into the base array
            $order = array_merge($columns, $order->attributesToArray());
        }

        return $order;
    }

    public function getOrdersWithGuests()
    {
        $orders = Order::with('guest', 'orderDetails')->get();

        // Transform the orders to include guest names instead of IDs
        $ordersWithGuestNames = $orders->map(function ($order) {
            $orderDetails = $order->orderDetails->map(function ($orderDetail) {
                $drink = Drink::find($orderDetail->drink_unit_id);
                $drinkName = $drink->name;
                $unit = $drink->units;

                return [
                    'id' => $orderDetail->id,
                    'order_id' => $orderDetail->order_id,
                    'drink_unit_id' => $orderDetail->drink_unit_id,
                    'amount' => $orderDetail->amount,
                    'promo_id' => $orderDetail->promo_id,
                    'unit_price' => $orderDetail->unit_price,
                    'discount' => $orderDetail->discount,
                    'receipt_id' => $orderDetail->receipt_id,
                    'created_at' => $orderDetail->created_at,
                    'updated_at' => $orderDetail->updated_at,
                    'drink_name' => $drinkName,
                    'unit' => $unit,
                ];
            });

            return [
                'id' => $order->id,
                'guest_id' => $order->guest_id,
                'guest_name' => $order->guest->name,
                'recorded_by' => $order->recorded_by,
                'recorded_at' => $order->recorded_at,
                'made_by' => $order->made_by,
                'made_at' => $order->made_at,
                'status' => $order->status,
                'table' => $order->table,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'order_details' => $orderDetails,
            ];
        });

        return $ordersWithGuestNames;
    }


    public function orderUpdate(Request $request, Order $order)
    {
        $valid = $request->validate([
            'guest_id' => 'integer|required',
            'recorded_by' => 'integer|sometimes',
            'recorded_at' => 'datetime|sometimes',
            'made_by' => 'integer|sometimes|nullable',
            'made_at' => 'datetime|sometimes|nullable',
            'served_by' => 'integer|sometimes|nullable',
            'served_at' => 'datetime|sometimes|nullable',
            'table' => 'string|sometimes|nullable',
            'status' => 'string|required', // Hozzáadva a státusz validációja
        ]);

        // Az Order modellben lévő státusz attribútum frissítése
        $order->status = $valid['status'];
        $order->fill($valid)->save();
        return $order;
    }

    public function placeOrder(Request $request, $userId)
    {
        $cartItems = $request->input('cartItems');
        $user = $request->user();
        // Create a new order
        $order = Order::create([
            'guest_id' => $userId,
            'recorded_by' => $userId,
            'recorded_at' => now(),
        ]);



        // Create order details for each item in cartItems
        $orderDetails = [];
        foreach ($cartItems as $key => $quantity) {
            list($drink_id, $amount, $unit) = explode('|', $key);
            // Example: Extracted values are ['89', '0.33', 'l'] for key '89|0.33|l'

            $unitPrice = DrinkUnit::where('drink_id', $drink_id)
                ->value('unit_price');


            $orderDetails[] = [
                'order_id' => $order->id,
                'drink_unit_id' => $drink_id,
                'amount' => $amount,
                'promo_id' => null, // You can set this based on your logic
                'unit_price' => $unitPrice, // You can set this based on your logic
                'discount' => 0,
                'receipt_id' => null,
            ];
        }

        // Save all order details
        OrderDetail::insert($orderDetails);

        return response()->json(['message' => 'Order placed successfully'], 201);
    }
    public function getOrderStats(Request $request)
    {
        try {
            $date = $request->input('date', date('Y-m-d')); // Default to current date if not provided
            $startOfDay = $date . ' 00:00:00';
            $endOfDay = $date . ' 23:59:59';
           /*  $backgroundColor = ["#4e73df", "#1cc88a", "#36b9cc"];
            $hoverBackgroundColor = ["#2e59d9", "#17a673", "#2c9faf"];
            $hoverBorderColor = "rgba(234, 236, 244, 1)"; */

            $recordedOrders = Order::whereBetween('recorded_at', [$startOfDay, $endOfDay])->count();
            $inProgressOrders = Order::whereBetween('made_at', [$startOfDay, $endOfDay])->count();
            $servedOrders = Order::whereBetween('served_at', [$startOfDay, $endOfDay])->count();
            $lateServedOrders = Order::whereBetween('served_at', [$startOfDay, $endOfDay])
                ->whereRaw('TIMESTAMPDIFF(MINUTE, recorded_at, served_at) > ?', [10])
                ->count();

            return response()->json([
                'date' => $date,
                'recorded' => $recordedOrders,
                'in_progress' => $inProgressOrders,
                'served' => $servedOrders,
                'late_served' => $lateServedOrders,
              /*   'backgroundColor'=>$backgroundColor,
                'hoverBackgroundColor'=>$hoverBackgroundColor,
                'hoverBorderColor'=>$hoverBorderColor, */
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
}
