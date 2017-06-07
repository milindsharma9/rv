<?php

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = array(
            ['id' => 1, 'name' => 'Event 1', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 2, 'name' => 'Event 2', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
        DB::table('events')->insert($events);
    }
}
