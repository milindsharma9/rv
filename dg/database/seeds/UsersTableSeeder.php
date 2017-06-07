<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('users')->delete();
        $users = array(
            ['id' => 1, 'first_name' => 'Admin', 'last_name' => 'LastName' , 'email' => 'admin@admin.com', 'password' => bcrypt('12222'), 'phone' => '981881818181', 'fk_users_role' => '1', 'remember_token' => '','created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 2, 'first_name' => 'Vendor', 'last_name' => 'LastName' , 'email' => 'vendor@vendor.com', 'password' => bcrypt('12222'), 'phone' => '981881818181', 'fk_users_role' => '2', 'remember_token' => '','created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 3, 'first_name' => 'Customer', 'last_name' => 'LastName' , 'email' => 'customer@customer.com', 'password' => bcrypt('12222'), 'phone' => '981881818181', 'fk_users_role' => '3', 'remember_token' => '','created_at' => new DateTime, 'updated_at' => new DateTime],
        );
        DB::table('users')->insert($users);
    }
}
