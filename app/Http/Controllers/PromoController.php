<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PromoController extends Controller
{
    protected static $valid_withs = ['category'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $with = [];

        if ($request->with) {
            $with = array_intersect(explode(',', strtolower($request->with)), self::$valid_withs);
        }
        return Promo::with($with)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response($request->all(), 404);
        $valid = $request->validate([
            'start' => 'date|required',
            'end' => 'date|sometimes|after:start',
            'category_id' => 'integer|required',
        ]);
        $promo = new Promo();
        $promo->fill($valid)->save();
        return $promo;
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
        return Promo::with($with)->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo)
    {
        $valid = $request->validate([
            'start' => 'date|required',
            'end' => 'date|sometimes|after:start',
            'category_id' => 'integer|required',
        ]);

        $promo->fill($valid)->save();
        return $promo;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        return $promo->delete();
    }

    public function scheme()
    {
        $promo = Promo::firstOrNew();

        // if an existing record was found
        if ($promo->exists) {
            $promo = $promo->attributesToArray();
        } else { // otherwise a new model instance was instantiated
            // get the column names for the table
            $columns = Schema::getColumnListing($promo->getTable());

            // create array where column names are keys, and values are null
            $columns = array_fill_keys($columns, null);

            // merge the populated values into the base array
            $promo = array_merge($columns, $promo->attributesToArray());
        }

        return $promo;
    }
}
