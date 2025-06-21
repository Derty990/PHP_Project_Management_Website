<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'owner', 'display_name' => 'Właściciel']);
        Role::create(['name' => 'editor', 'display_name' => 'Edytor']);
        Role::create(['name' => 'member', 'display_name' => 'Członek']);
    }
}
