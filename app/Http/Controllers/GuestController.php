<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

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
        $valid = $request->validate([
            'first_name' => 'string|required',
            'middle_name' => 'string|nullable|sometimes',
            'last_name' => 'string|required',
            'email' => 'string|required|unique:guests,email',
            'password' => ['string', PasswordRule::min(10)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
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
        $valid = $request->validate([
            'first_name' => 'string|sometimes|min:2',
            'middle_name' => 'string|nullable|sometimes',
            'last_name' => 'string|sometimes|min:2',
            'email' => 'string|sometimes|required|unique:guests,email,' . $guest->id,
            'password' => ['sometimes', PasswordRule::min(10)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
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
            'first_name' => 'string|sometimes|required',
            'middle_name' => 'string|sometimes|nullable',
            'last_name' => 'string|sometimes|required',
            'email' => 'prohibited',
            'active' => 'prohibited',
            'password' => ['sometimes', PasswordRule::min(10)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],

        ]);

        $guest->fill($valid)->save();
        return $guest;
    }

}
