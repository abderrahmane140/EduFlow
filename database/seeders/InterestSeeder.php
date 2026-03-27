<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            ['name' => 'Web Development',   'slug' => 'web-development'],
            ['name' => 'Data Science',       'slug' => 'data-science'],
            ['name' => 'Mobile Development','slug' => 'mobile-development'],
            ['name' => 'Cybersecurity',      'slug' => 'cybersecurity'],
            ['name' => 'UI/UX Design',       'slug' => 'ui-ux-design'],
            ['name' => 'DevOps',             'slug' => 'devops'],
            ['name' => 'Artificial Intelligence', 'slug' => 'ai'],
        ];

        foreach ($interests as $interest) {
            Interest::create($interest);
        }
    }   
}
