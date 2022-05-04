<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Image::factory(10000)
        //     ->create()
        //     ->each(function ($image) {
        //         User::factory(1)
        //             ->create(['image_id' => $image->id]);
        //     });

        $images = Image::all(['id']);

        // for ($i=0; $i < 5; $i++) { 
            $users = User::factory(100000)->make([
                'image_id' =>  $images->random()->pluck('id')->first()
            ]);
    
            $chucks = $users->chunk(20000);
    
            $chucks->each(function ($chuck) {
                User::insert($chuck->toArray());
            });
        // }

        // $images = Image::all(['id']);

        // for ($i=0; $i < 7; $i++) { 
        //     $list = [];
        //     for ($j=0; $j < 10500; $j++) { 
        //         $list[] = [
        //             'username' => "user_ $i _ $j",
        //             'karma_score' => random_int(0, 9999),
        //             'image_id' => $images->random()->pluck('id')->first()
        //         ];
        //     }

        //     User::insert($list);
        // }
    }
}
