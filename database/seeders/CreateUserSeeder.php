<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
{
    $admin = User::create([
        'name' => 'tarek', 
        'email' => 'admin@adminable.com',
        'password' => bcrypt('eRROR404@'),
    ]);
    $admin->assignRole('admin');

    $commercial = User::create([
        'name' => 'ahmed', 
        'email' => 'ahmed@gmail.com',
        'password' => bcrypt('Password123!'),
    ]);

    $commercial->assignRole('commercial');

    $user = User::create([
        'name' => 'said', 
        'email' => 'said@gmail.com',
        'password' => bcrypt('12345678'),
    ]);

    $user->assignRole('user');
}

}