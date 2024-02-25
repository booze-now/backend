<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Employee::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response($request->all(), 404);
        $valid = $request->validate([
            'name' => 'string|required',
            'email' => 'string|required|unique:employees,email',
            'password' => [
                'string',
                'required',
                'min:10',             // legalább 10 karakter hosszú
                'regex:/[a-z]/',      // legalább egy kisbetű
                'regex:/[A-Z]/',      // legalább egy nagybetű
                'regex:/[0-9]/',      // legalább egy számjegy
                'regex:/[@+\-\.$!%*#?&]/', // legalább egy speciális karakter
            ],
            'role_code' => [
                'integer',
                'required',
                Rule::in(array_keys(Employee::ROLES))
            ],
            'active' => 'boolean|required',
        ]);
        $employee = new Employee();
        $employee->fill($valid)->save();
        return $employee;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        return Employee::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $valid = $request->validate([
            'name' => 'string|sometimes|required',
            'email' => 'string|sometimes|required|unique:employees,email',
            'password' => [
                'string',
                'required',
                'sometimes',
                'min:10',             // legalább 10 karakter hosszú
                'regex:/[a-z]/',      // legalább egy kisbetű
                'regex:/[A-Z]/',      // legalább egy nagybetű
                'regex:/[0-9]/',      // legalább egy számjegy
                'regex:/[@+\-\.$!%*#?&]/', // legalább egy speciális karakter
            ],
            'role_code' => [
                'integer',
                'required',
                Rule::in(array_keys(Employee::ROLES))
            ],
            'active' => 'boolean|sometimes|required',
        ]);

        $employee->fill($valid)->save();
        return $employee;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->delete())
            return response()->noContent();
    }

    public function scheme()
    {
        $employee = Employee::firstOrNew();

        // if an existing record was found
        if ($employee->exists) {
            $employee = $employee->attributesToArray();
        } else { // otherwise a new model instance was instantiated
            // get the column names for the table
            $columns = Schema::getColumnListing($employee->getTable());

            // create array where column names are keys, and values are null
            $columns = array_fill_keys($columns, null);

            // merge the populated values into the base array
            $employee = array_merge($columns, $employee->attributesToArray());
        }

        return $employee;
    }
}
