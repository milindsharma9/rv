<?php

use Illuminate\Database\Seeder;

class createRiderRoleSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $userRoles = array(
            ['id_users_role' => 4, 'name' => 'Driver', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
        DB::table('users_role')->insert($userRoles);
    }

}
