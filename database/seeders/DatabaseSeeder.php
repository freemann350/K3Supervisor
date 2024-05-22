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

        echo ("\n-- CLUSTERS --\n");
        echo ("ADDING CLUSTER 'Default Cluster'...");
        DB::table('clusters')->insert([
            'name' => 'Default Cluster',
            'user_id' => 1,
            'endpoint' => 'https://192.168.50.160:6443',
            'auth_type' => 'T',
            'token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6Il9qQmRWSnp5Z1Z5dWNnbzlNMVV0V19qZ2ZfeFNMSDFjczFRNmoxNG5ZTUkifQ.eyJpc3MiOiJrdWJlcm5ldGVzL3NlcnZpY2VhY2NvdW50Iiwia3ViZXJuZXRlcy5pby9zZXJ2aWNlYWNjb3VudC9uYW1lc3BhY2UiOiJkZWZhdWx0Iiwia3ViZXJuZXRlcy5pby9zZXJ2aWNlYWNjb3VudC9zZWNyZXQubmFtZSI6ImRlZmF1bHQtdG9rZW4iLCJrdWJlcm5ldGVzLmlvL3NlcnZpY2VhY2NvdW50L3NlcnZpY2UtYWNjb3VudC5uYW1lIjoiZGVmYXVsdCIsImt1YmVybmV0ZXMuaW8vc2VydmljZWFjY291bnQvc2VydmljZS1hY2NvdW50LnVpZCI6ImI3Y2ZmMDU4LWRiYzUtNGNmNS04ZGNhLTQ1Y2Q5ZDkyY2FmZCIsInN1YiI6InN5c3RlbTpzZXJ2aWNlYWNjb3VudDpkZWZhdWx0OmRlZmF1bHQifQ.rK2Au6sasWoY2v2ZLPrC2kil_xVTpEy0x1ipc69e7EfUkOrBZQP8thKPny4Ru7tz_fzqRTOPbew-DsKZeMpkl-362mfS8cq0oVH0GVq-Gn4v7yPKWxpswEjf1cruHYVtJEA0OlqKXM1nkz0tV3Bo8BrREJZJXrOYkJmMo6UanluSN3sKlf_uUjtEJF3pD3kzIv52V54XiqznaoJlVxdE-bjnWCuNQTiE_p1RYckFqdKIv-IM2FDW9YR2yxv4G0-DwxCuCcEPuubWgin5rs1pRyXSiaeEfUIsmrk4ByiO9WunAkMqXieqv1gOLzfUr3joQv3JQMeZCK1UNm05kDu5QA',
            'timeout' => 5,
        ]);
        echo ("[OK]\n");
        echo ("\nSEEDING COMPLETE\n\n");
    }
}
