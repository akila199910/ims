<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RefreshPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Getting all permissions
        $permissions = Permission::pluck('name')->toArray();

        // Getting the users who has admin and super admin roles
        $users = User::role(['super_admin', 'admin'])->get();

        if(count($permissions) && count($users))
        {
            foreach ($users as $key => $user)
            {
                $user->syncPermissions($permissions);
            }
        }
    }
}
