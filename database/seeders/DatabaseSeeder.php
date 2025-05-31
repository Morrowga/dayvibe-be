<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $data = [
            [
                "name_en" => "Sticker",
                "name_mm" => "စတေကာ"
            ],
            [
                "name_en" => "Poster",
                "name_mm" => "ပိုစတာ"
            ]
        ];

        Category::insert($data);
    }
}
