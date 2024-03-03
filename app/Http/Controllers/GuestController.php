<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Guest::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response($request->all(), 404);
        $valid = $request->validate([
            'name' => 'string|required',
            'email' => 'string|required|unique:guests,email',
            'password' => [
                'string',
                'required',
                'min:10',             // legalább 10 karakter hosszú
                'regex:/[a-z]/',      // legalább egy kisbetű
                'regex:/[A-Z]/',      // legalább egy nagybetű
                'regex:/[0-9]/',      // legalább egy számjegy
                'regex:/[@+\-\.$!%*#?&]/', // legalább egy speciális karakter
            ],
            'active' => 'boolean|required',
        ]);
        $guest = new Guest();
        $guest->fill($valid)->save();
        return $guest;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        return Guest::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $guest->Auth::user();
        $valid = $request->validate([
            'name' => 'string|sometimes|required',
            'email' => 'string|sometimes|required|unique:guests,email',
            'password' => [
                'string',
                'required',
                'confirmed',
                'sometimes',
                'min:10',             // legalább 10 karakter hosszú
                'regex:/[a-z]/',      // legalább egy kisbetű
                'regex:/[A-Z]/',      // legalább egy nagybetű
                'regex:/[0-9]/',      // legalább egy számjegy
                'regex:/[@+\-\.$!%*#?&]/', // legalább egy speciális karakter
            ],
            'active' => ['boolean','sometimes','required',Rule::in([Guest::INACTIVE, Guest::ACTIVE])],
        ]);

        $guest->fill($valid)->save();
        return $guest;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        if ($guest->delete())
            return response()->noContent();
    }

    public function scheme()
    {
        $guest = Guest::firstOrNew();

        // if an existing record was found
        if ($guest->exists) {
            $guest = $guest->attributesToArray();
        } else { // otherwise a new model instance was instantiated
            // get the column names for the table
            $columns = Schema::getColumnListing($guest->getTable());

            // create array where column names are keys, and values are null
            $columns = array_fill_keys($columns, null);

            // merge the populated values into the base array
            $guest = array_merge($columns, $guest->attributesToArray());
        }

        return $guest;
    }

    public function me(Request $request) {
        return Auth::user()->makeVisible(['created_at']);
    }

    public function updateSelf(Request $request)
    {
        $guest = Guest::find(Auth::user()->id);
        $valid = $request->validate([
            'name' => 'string|sometimes|required',
            'email' => 'prohibited',
            'active' => 'prohibited',
            'password' => [
                'string',
                'required',
                'confirmed',
                'sometimes',
                'min:10',             // legalább 10 karakter hosszú
                'regex:/[a-z]/',      // legalább egy kisbetű
                'regex:/[A-Z]/',      // legalább egy nagybetű
                'regex:/[0-9]/',      // legalább egy számjegy
                'regex:/[@+\-\.$!%*#?&]/', // legalább egy speciális karakter
            ],
        ]);

        $guest->fill($valid)->save();
        return $guest;
    }

}
