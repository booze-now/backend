<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DrinkCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;



class DrinkControllerTest extends TestCase
{
    //use RefreshDatabase;
    use DatabaseMigrations;

    protected static $sampleDrink;

    /**
     * A basic feature test example.
     */

    protected $seed = false;

    protected $employee;
    protected $guest;

    protected function setUp(): void
    {
        parent::setUp();
        echo "\nsetup\n";
        DB::connection()->getSchemaBuilder()->enableForeignKeyConstraints();
        // Create a user for authentication
        $this->employee = \App\Models\Employee::factory()->create();

        $this->guest = \App\Models\Guest::factory()->create();
    }
    public function test_we_can_create_drink_category(): void
    {

        $this->actingAs($this->employee, 'employee');
        // echo json_encode($this->employee);
        Auth::shouldUse('employee');

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

        $response = $this->post('/api/staff/categories', [
            'name_en' => self::$sampleDrink->category->name_en,
            'name_hu' => self::$sampleDrink->category->name_hu,
        ]);
        // echo $response->getContent();
        $response->assertStatus(201);
        $category = DrinkCategory::get()->last();
        self::$sampleDrink->category->id = $category->id;
    }

    public function test_we_can_create_drink(): void
    {
        // echo $this->employee->name;

        $this->actingAs($this->employee);
        $category = DrinkCategory::factory()->create();

        $response = $this->post('/api/staff/drinks', [
            'name_en' => self::$sampleDrink->name_en,
            'name_hu' => self::$sampleDrink->name_hu,
            'description_en' => self::$sampleDrink->description_en,
            'description_hu' => self::$sampleDrink->description_hu,
            'category_id' => $category->id,// self::$sampleDrink->category->id,
        ]);

        $response->assertStatus(201);

    }
    public function test_index()
    {
        $this->actingAs($this->employee);

        $response = $this->get('/api/staff/drinks');
        $response->assertStatus(200);
    }

    public function test_store()
    {
        $this->actingAs($this->employee);
        $category = DrinkCategory::factory()->create();
        // echo json_encode($category);

        $drinkData = [
            'name_en' => 'Test Drink',
            'name_hu' => 'Teszt Ital',
            'category_id' => $category->id,
            'active' => true,
        ];

        $response = $this->post('/api/staff/drinks', $drinkData);
        $response->assertStatus(201); // 201 Created
    }

    public function test_show()
    {
        $drink = \App\Models\Drink::factory()->create();

        $response = $this->get("/api/staff/drinks/{$drink->id}");
        $response->assertStatus(200);
    }

    public function test_update()
    {
        $drink = \App\Models\Drink::factory()->create();

        $updatedData = [
            'name_en' => 'Updated Name',
            'active' => false
            // Add more data as needed
        ];

        $response = $this->put("/api/staff/drinks/{$drink->id}", $updatedData);
        $response->assertStatus(200);
    }

    public function test_destroy()
    {
        $drink = \App\Models\Drink::factory()->create();

        $response = $this->delete("/api/staff/drinks/{$drink->id}");
        $response->assertStatus(204); // 204 No Content
    }

    public function test_scheme()
    {
        $response = $this->get('/api/staff/drinks/scheme');
        $response->assertStatus(200);
    }
}