<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ImageSeeder::class,
            UserSeeder::class
        ]);

        // for ($i=0; $i < 10; $i++) { 
        //     $listOfImages = [];
        //     for ($i=0; $i < 1000; $i++) { 
                
        //     }
        // }

    }
}
