<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Helper;

use App\Http\Helper\CommonHelper;
use Exception;
use Maknz\Slack\Facades\Slack;

class SlackService {

    /**
     * Method to send slack notifications
     *
     * @return array
     */
    public static function sendSlackNotification() {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
            'data'      => array()
        );
        try {
            $enableSlack    = env('SLACK_ENABLE', "0");
            $slackBaseUrl   = env('PUBLIC_URL', "");
            if ($enableSlack == '1') {
                $message        = "@channel\n\nA new order just came through!\n\nFROM ALCHEMY WINGS\n\nLog on to <".$slackBaseUrl."> process the order";
                Slack::send($message);
            }
            $response['status'] = true;
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile(). "|" . $ex->getLine(), CommonHelper::EMAIL_FAILURE_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

}
