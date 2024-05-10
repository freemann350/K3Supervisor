<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo ("-- DELETING EXISTING DATA --\n");
        echo ("DELETING USER DATA...");
        DB::delete('delete from users');
        echo (" [OK]\n");

        echo ("\n-- USERS --\n");
        echo ("ADDING USER 'Admin User'...");
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'A',
            'resources' => '*',
            'verbs' => '*'
        ]);
        echo ("[OK]\n");

        echo ("ADDING USER 'Regular User'...");
        DB::table('users')->insert([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'U',
            'resources' => '*',
            'verbs' => 'get'
        ]);
        echo ("[OK]\n");

        echo ("\nSEEDING COMPLETE\n\n");
    }
}
