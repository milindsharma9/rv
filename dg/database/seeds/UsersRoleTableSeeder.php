<?php

use Illuminate\Database\Seeder;
 
class UsersRoleTableSeeder extends Seeder {
 
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        //DB::table('users_role')->delete();
        $userRoles = array(
            ['id_users_role' => 1, 'name' => 'Admin', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id_users_role' => 2, 'name' => 'Vendor', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id_users_role' => 3, 'name' => 'Customer', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
        DB::table('users_role')->insert($userRoles);
    }
 
}

