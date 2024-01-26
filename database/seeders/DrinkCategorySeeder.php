<?php

namespace Database\Seeders;

use App\Models\DrinkCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as File;

class DrinkCategorySeeder extends Seeder
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
        if (file_exists("database/seeders/data/DrinkCategories.json")) {
            DrinkCategory::truncate();
            $json = File::get("database/seeders/data/DrinkCategories.json");
            $categories = json_decode($json);
            $this->saveCategories($categories);
        }
    }

    protected function saveCategories($categories, $parent_id = null)
    {
        foreach ($categories as $key => $value) {

            $drink = DrinkCategory::create([
                'name_en' => $value->en,
                'name_hu' => $value->hu,
                'parent_id' => $parent_id,
            ]);

            if ($value->children ?? false) {
                $this->saveCategories($value->children, $drink->id);
            }
        }
    }
}


