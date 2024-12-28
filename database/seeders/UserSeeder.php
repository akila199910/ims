<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'name' => 'Super Admin',
                'contact' => '0111111111',
                'email' => 'admin@webappclouds.com',
                'password' => Hash::make('Admin@1234')
            ],
            [
                'first_name' => 'Dilan',
                'last_name' => 'Webappclouds',
                'name' => 'Dilan Webappclouds',
                'contact' => '2672074190',
                'email' => 'dilan@webappclouds.com',
                'password' => Hash::make('Dilan@1234')
            ],
            [
                'first_name' => 'Saseenthiran',
                'last_name' => 'Admin',
                'name' => 'Saseenthiran Admin',
                'contact' => '0769632535',
                'email' => 'saseenthiran1995@gmail.com',
                'password' => Hash::make('Sasi@123')
            ]
        ];

        //$permisson
        $permissions = Permission::pluck('name')->toArray();

        foreach($admin as $item)
        {
            $admin = new User();
            $admin->first_name = $item['first_name'];
            $admin->last_name = $item['last_name'];
            $admin->name = $item['name'];
            $admin->contact = $item['contact'];
            $admin->email = $item['email'];
            $admin->password = $item['password'];
            $admin->status = 1;
            $admin->save();

            $profile = new UserProfile();
            $profile->user_id = $admin->id;
            $profile->profile = 'user/user.png';
            $profile->save();

            //assign role
            $admin->assignRole('super_admin');

            //Give Permission
            $admin->givePermissionTo($permissions);
        }
    }


}
