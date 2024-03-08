<?php

namespace App\Console\Commands;

use App\Models\Drink;
use App\Models\DrinkCategory;
use App\Models\DrinkUnit;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:categories {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import drink categories from a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $this->error("File not found at $filePath");
            return;
        }

        // $jsonContents = file_get_contents($filePath);
        $jsonContents = File::get($filePath);
        $categories = json_decode($jsonContents);

        DB::beginTransaction();
        try {
            $this->saveCategories($categories);
            DB::commit();
            $this->info("Drink categories imported successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("An error occurred: " . $e->getMessage());
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