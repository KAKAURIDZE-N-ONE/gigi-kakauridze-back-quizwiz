<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('footer')->insert([
            'email' => 'quizwiz@gmail.com',
            'phone' => '+995 328989',
            'facebook' => 'https://www.facebook.com/gigi.kakauridze.1/',
            'linkedin' => 'https://www.linkedin.com/in/gigi-kakauridze-a3aab527b/',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
