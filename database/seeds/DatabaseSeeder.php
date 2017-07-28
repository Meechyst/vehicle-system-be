<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 10)->create();
        factory(App\Brand::class, 30)->create();
        factory(App\Type::class, 30)->create();
        factory(App\Vmodel::class, 30)->create();
        factory(App\Vehicle::class, 30)->create();
    }
}
