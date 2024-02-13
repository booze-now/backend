<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Transliterator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $gender = rand(0, 1) ? 'male' : 'female';

        $last_name = fake()->lastName();
        $last_name .= rand(0, 4) ? '' : '-' . fake()->lastName();

        $first_name = fake()->firstName($gender);
        $middle_name = rand(0, 1) ? fake()->firstName($gender) : '';
        $name = implode(' ', array_filter([$last_name, $first_name, $middle_name]));
        $email = strtolower(str_replace(' ', '.', $this->stripAccents($name))) . '@example.com';
        return [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('Password1!'),
            'table' => null,
            'reservee' => false,
            'active' => 1,

        ];
    }

    function stripAccents($string)
    {
        $transliterator = Transliterator::createFromRules(':: NFD; :: [:Mn:] Remove; :: NFC;');
        return  $transliterator->transliterate($string);
    }
}
