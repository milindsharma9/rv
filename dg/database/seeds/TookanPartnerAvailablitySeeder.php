<?php

use Illuminate\Database\Seeder;

class TookanPartnerAvailablitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $insertData = array();
        $aTime = config('appConstants.site_timings');
        $aDay = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];
        foreach ($aDay as $day) {
            foreach ($aTime as $key => $val) {
                $open_close_time = explode("-", $key);
                $aData = array(
                    'day'           => $day,
                    'open_time'     => !empty($open_close_time[0]) ? $open_close_time[0] : '00:00',
                    'close_time'    => !empty($open_close_time[1]) ? $open_close_time[1] : '00:00',
                );
                array_push($insertData, $aData);
            }
        }
        DB::table('tookan_partner_availability')->insert($insertData);
    }
}
