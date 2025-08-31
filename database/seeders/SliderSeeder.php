<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Slider::create([
                'banner' => "sliders/1756641578.png",
                'type' => 'Type ' . ($i + 1),
                'title' => 'Slider Title ' . ($i + 1),
                'starting_price' => '$' . (100 + $i * 10),
                'btn_url' => 'https://example.com/slider' . ($i + 1),
                'serial' => $i + 1,
                'status' => true,
            ]);
        }
    }
}
