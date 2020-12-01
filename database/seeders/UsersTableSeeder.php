<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@lex.test',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'customer',
            'email' => 'customer@lex.test',
            'password' => bcrypt('password'),
        ]);
    }
}
