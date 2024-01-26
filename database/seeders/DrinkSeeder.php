<?php

namespace Database\Seeders;

use App\Models\Drink;
use App\Models\DrinkCategory;
use App\Models\DrinkUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFromJson();
    }

    protected function seedFromJson()
    {
        if (file_exists("database/seeders/data/Drinks.json")) {
            Drink::truncate();
            $json = File::get("database/seeders/data/Drinks.json");
            $drinks = json_decode($json);
            $this->saveDrinks($drinks);
        }
    }

    protected function saveDrinks($categories, $parent_id = null)
    {
        foreach ($categories as $key => $value) {

            $category = DrinkCategory::where('name_en', $value->category_en);
            if ($category->count() == 0) {
                die("Hiányzik: " . $value->category_en);
            }
            $category = $category->pluck('id')[0];

            $drink = Drink::create([
                'name_en' => $value->en,
                'name_hu' => $value->hu,
                'description_en' => $value->description_en ?? null,
                'description_hu' => $value->description_hu ?? null,
                'category_id' => $category,
                'status' => $value->status_hu,
            ]);
            foreach ($value->units as $unit) {
                DrinkUnit::create([
                    'drink_id' => $drink->id,
                    'amount' => $unit->amount,
                    'unit_en' => $unit->unit_en,
                    'unit_hu' => $unit->unit_hu,
                    'unit_price' => $unit->unit_price,
                    'status' => 'aktív',
                ]);
            }

            if ($value->children ?? false) {
                $this->saveDrinks($value->children, $drink->id);
            }
        }
    }
}
