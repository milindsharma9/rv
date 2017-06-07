<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Exception;
use App\Http\Helper\CommonHelper;

class PartnerAvailability extends Model
{
    //
    
    const TASK_INTERNAL = 'internal';
    const TASK_PARTNER  = 'partner';
    
    /**
     *
     * @var table name 
     */
    protected $table = "tookan_partner_availability";
    
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['day', 'task_type', 'open_time', 'close_time'];

    /**
     * Method to update tookan partner availability
     *
     * @param array $data
     */
     public static function updateTookanTimeEntries(array $data) {
        try {
            DB::beginTransaction();
            $insertData = array();
            foreach ($data['site_time'] as $key => $val) {
                $open_close_time = explode("-", $key);
                $aData = array(
                    'day'           => isset($data['theDay']) ? $data['theDay'] : '',
                    'open_time'     => !empty($open_close_time[0]) ? $open_close_time[0] : '00:00',
                    'close_time'    => !empty($open_close_time[1]) ? $open_close_time[1] : '00:00',
                    'task_type'     => $data['partner_'.$key]
                );
                array_push($insertData, $aData);
            }
            DB::table('tookan_partner_availability')->where([['day', $data['theDay']],
            ])->delete();
            DB::table('tookan_partner_availability')->insert($insertData);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
    }

    /**
     * Method to get Tookan Avalability
     * 
     * @param string $dayName
     * @return array
     */
    public static function getTookanAvailability($dayName) {
        $aResponse = array(
            'data'      => '',
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        try {
            $data = DB::table('tookan_partner_availability AS tpa')
                        ->select("tpa.day AS day")
                        ->addSelect('tpa.id')
                        ->addSelect('tpa.task_type')
                        ->addSelect(DB::raw("SUBSTR(tpa.close_time,1,5) AS close_time"))
                        ->addSelect(DB::raw("SUBSTR(tpa.open_time,1,5) AS open_time"))
                        ->where('tpa.day', '=', $dayName)->get();
            $aResponse['data']      = $data;
            $aResponse['status']    = true;
            $aResponse['message']   = trans('messages.store_time_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }

    /**
     * Method to get Tookan Task Type during particular time slot for order.
     *
     * @return string $taskType
     */
    public static function getTookanAvailabilityType() {
        $hourTime        = date("H:i:s");
        $minOpenTime     = "30";
        $timeAfterBuffer    = date("H:i:s", strtotime('+' . $minOpenTime . ' minutes', strtotime($hourTime)));
        $timeBeforeBuffer   = date("H:i:s", strtotime('-' . $minOpenTime . ' minutes', strtotime($hourTime)));
        $aAvailability = DB::table('tookan_partner_availability')
                ->select('tookan_partner_availability.*')
                ->whereBetween('open_time', [$timeBeforeBuffer, $timeAfterBuffer])
                ->whereRaw('day = DAYNAME(NOW())')
                ->get();
        $taskType = static::TASK_INTERNAL;
        foreach ($aAvailability as $availability) {
            $openTime   = $availability->open_time;
            $closeTime  = $availability->close_time;
            if (strtotime($hourTime) >= strtotime($openTime) && strtotime($hourTime) < strtotime($closeTime)) {
                $taskType = $availability->task_type;
                break;
            }
        }
        return $taskType;
    }
}
