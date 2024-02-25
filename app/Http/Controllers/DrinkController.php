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
        // return response($request->all(), 404);
        $valid = $request->validate([
            'name_en' => 'string|required|unique:drinks,name_en',
            'name_hu' => 'string|required|unique:drinks,name_hu',
            'category_id' => 'integer|required',
            'description_en' => 'string|sometimes|nullable',
            'description_hu' => 'string|sometimes|nullable',
            'active' => 'boolean|sometimes',
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
        return Drink::with($with)->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Drink $drink)
    {
        $valid = $request->validate([
            'name_en' => 'string|sometimes|unique:drinks,name_en',
            'name_hu' => 'string|sometimes|unique:drinks,name_hu',
            'category_id' => 'integer|sometimes',
            'description_en' => 'string|sometimes|nullable',
            'description_hu' => 'string|sometimes|nullable',
            'active' => 'boolean|sometimes',
        ]);

        $drink->fill($valid)->save();
        return $drink;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drink $drink)
    {
        if ( $drink->delete()) return response()->noContent();
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

    public function menu(Request $request)
    {
        return Drink::get()->append('category_name')->append('units')->makeHidden(['category']);

    }

    public function menuTree(Request $request)
    {
        $categories = \App\Models\DrinkCategory::all();
        $drinks = Drink::get()->append('category_name')->append('units')->makeHidden(['category']);


        $tree = [];

        foreach($categories as $category) {
            $cat = (object)($category->toArray());

            $cat->drinks = array_filter($drinks->toArray(), function ($d) use ($cat) {return $d['category_id'] == $cat->id;});

            if ($cat->parent_id === null) {
                $tree[$cat->id] = $cat;
                $cat->subcategory = [];
            } else {
                $tree[$cat->parent_id]->subcategory[$cat->id] = $cat;
            }
            unset($cat->parent_id);
        }

        return $tree;
    }
}
