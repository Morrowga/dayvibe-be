<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Day Vibe',
            'email' => 'syu@syu.com',
            'password' => Hash::make('199818')
        ]);

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

        foreach ($data as $item) {
            Category::firstOrCreate($item);
        }
    }
}
