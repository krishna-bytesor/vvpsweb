<?php

namespace Database\Seeders;

use App\Models\PostType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "Audio",
            "Books",
            "Granthraj",
            "Pada seva",
            "Pearls",
            "Photos",
            "Videos",
            "Vyasa Puja",
            "Yatras",
            "Calendar-Quotes",
            "Verse of the Day",
            "Pearls of Wisdom"
        ];

        foreach ($data as $index => $type) {
            PostType::updateOrCreate([
                "id" => $index+1,
            ],
            [
                'name' => $type,
                'slug' => Str::slug($type)
            ]);
        }
    }
}
