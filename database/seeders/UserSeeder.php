<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'name' => 'Douglas Corona',
            'phone' => '04243827364',
            'email' => 'corona@gmail.com',
            'profile' => 'ADMIN',
            'status' => 'ACTIVE',
            'password' => bcrypt('123'),
        ]);
         User::create([
            'name' => 'Jhoy Corona',
            'phone' => '04241234234',
            'email' => 'jhoy@gmail.com',
            'profile' => 'EMPLOYEE',
            'status' => 'ACTIVE',
            'password' => bcrypt('123'),
        ]);
    }
}
