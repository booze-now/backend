<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DrinkController extends Controller
{
    protected static $valid_withs = ['category', 'units'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $with = [];

        if ($request->with) {
            $with = array_intersect(explode(',', strtolower($request->with)), self::$valid_withs);
        }
        return Drink::with($with)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'name_en' => 'string|required|unique:drink,name_en',
            'name_hu' => 'string|required|unique:drink,name_hu',
            'category_id' => 'required|integer',
            'description_en' => 'sometimes|nullable|boolean',
            'description_hu' => 'sometimes|nullable|boolean',
            'status' => ['required', 'string', Rule::in(Drink::getStatuses())],
        ]);
        $drink = new Drink();
        $drink->fill($valid)->save();
        return $drink;
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
        return Drink::with($with)->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Drink $drink)
    {
        $valid = $request->validate([
            'name_en' => 'sometimes|unique',
            'name_hu' => 'sometimes|unique',
            'category_id' => 'sometimes|integer',
            'description_en' => 'sometimes|nullable|string',
            'description_hu' => 'sometimes|nullable|string',
            'status' => ['sometimes', 'string', Rule::in(Drink::getStatuses())],
        ]);

        $drink->fill($valid)->save();
        return $drink;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drink $drink)
    {
        return $drink->delete();
    }

    public function scheme()
    {
        $drink = Drink::firstOrNew();

        // if an existing record was found
        if ($drink->exists) {
            $drink = $drink->attributesToArray();
        } else { // otherwise a new model instance was instantiated
            // get the column names for the table
            $columns = Schema::getColumnListing($drink->getTable());

            // create array where column names are keys, and values are null
            $columns = array_fill_keys($columns, null);

            // merge the populated values into the base array
            $drink = array_merge($columns, $drink->attributesToArray());
        }

        return $drink;
    }
}
