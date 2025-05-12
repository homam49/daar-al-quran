<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'moderator'],
            ['name' => 'admin'],
            ['name' => 'teacher'],
            ['name' => 'student']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
