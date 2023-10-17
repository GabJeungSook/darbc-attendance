<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role_id' => 1,
            'name' => 'DARBC Admin',
            'email' => 'darbcadmin@darbc.com',
            'username' => 'DARBCMEMBERSHIP',
            'password' => Hash::make('attendance'),
        ]);
    }
}
