<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
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
            'made_by' => 'integer|sometimes',
            'made_at' => 'datetime|sometimes',
            'served_by' => 'integer|sometimes',
            'served_at' => 'datetime|sometimes',
            'table' => 'string|sometimes',
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
            'made_by' => 'integer|sometimes',
            'made_at' => 'datetime|sometimes',
            'served_by' => 'integer|sometimes',
            'served_at' => 'datetime|sometimes',
            'table' => 'string|sometimes',
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
}
