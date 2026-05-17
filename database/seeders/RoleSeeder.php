<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name'        => 'Developer',
                'guard_name'  => 'web',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);
    }
}