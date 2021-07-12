<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory;
use Faker\Provider\Uuid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $posts = [];
        $i     = 0;

        for ($i = 0; $i <= 10; $i++) {
            $posts[] = [
                "post_uid" => Uuid::uuid(),
                "title" => $faker->realText(50, 2),
                "content" => $faker->realTextBetween(160, 5000, 2),
                'created_at' => Carbon::now()->toDateTimeString(),
                "created_by" => "1"
            ];
        }

        DB::table('posts')->insert($posts);

        /*
        print_r($posts);
        echo "\n";
        */
    }
}
