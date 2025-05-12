<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ModeratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moderatorRole = Role::where('name', 'moderator')->first();

        User::create([
            'name' => 'المشرف الرئيسي',
            'email' => 'moderator@daaralquran.com',
            'password' => Hash::make('password'),
            'role_id' => $moderatorRole->id,
            'is_approved' => true,
        ]);
    }
}
