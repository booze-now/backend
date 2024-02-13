<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class DrinkControllerTest extends TestCase
{
    // use RefreshDatabase;
    use DatabaseMigrations;

    protected static $sampleDrink;

    /**
     * A basic feature test example.
     */

    protected $seed = false;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_we_can_create_drink_category(): void
    {
        self::$sampleDrink = (object)([
            'id' => null,
            'name_en' => 'Tap Water',
            'name_hu' => 'Csapvíz',
            'description_en' => 'Jummy water, just from the tap.',
            'description_hu' => 'Fincsi csapvíz.',
            'category' => (object)[
                'id' => null,
                'name_en' => 'Víz',
                'name_hu' => 'Water',
            ]
        ]);

        $response = $this->post('/api/categories', [
            'name_en' => self::$sampleDrink->category->name_en,
            'name_hu' => self::$sampleDrink->category->name_hu,
        ]);
        $response->assertStatus(201);
        $category = \App\Models\DrinkCategory::get()->last();
        self::$sampleDrink->category->id = $category->id;
    }

    public function test_we_can_create_drink(): void
    {
        $response = $this->post('/api/drinks', [
            'name_en' => self::$sampleDrink->name_en,
            'name_hu' => self::$sampleDrink->name_hu,
            'description_en' => self::$sampleDrink->description_en,
            'description_hu' => self::$sampleDrink->description_hu,
            'category_id' => self::$sampleDrink->category->id,
        ]);

        $response->assertStatus(201);
    }

}
