<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        Model::unguard();
        $this->call('UsersRoleTableSeeder');
        $this->call('UsersTableSeeder');
        $this->call('EventsTableSeeder');
        $this->call('createRiderRoleSeeder');
        $this->call('OrderStatusSeeder');
        $this->call('FaqSeeder');
        $this->call('TookanPartnerAvailablitySeeder');
    }
}