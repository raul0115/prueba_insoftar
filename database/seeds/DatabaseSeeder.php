<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,1)->create([
            'name' => 'Raul',
            'email' => 'admin@insoftar.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'remember_token' => str_random(10),
            'admin'=> 1
        ]);
        factory(User::class,50)->create();
     
    }
}
