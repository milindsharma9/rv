<?php

namespace App\Http\Helpers;

use Exception;
use Log;
use Mandrill;

class Email {

    /**
     * Send Email on the basis of details provided.
     * 
     * @param string $toEmail
     * @param array $data
     * @param string $templateName
     * @param string $subject
     * @param string $from
     * @param string $fromName
     * @return mixed
     */
    public static function sendEmail($toEmail, $mergeVars, $templateName, $addBcc = NULL, $subject = NULL, $from = NULL, $replyTo = NULL, $fromName = 'Alchemy Team') {
        if (NULL == $from) {
            $from = env('MAIL_USERNAME');
        }
        if (NULL == $replyTo) {
            $from = env('MAIL_USERNAME');
        }
        try {

            $mandrill = new Mandrill(env('MAIL_PASSWORD'));
            $message = array(
                /* Subject, from_email & from_name are now configurable from mandrillapp.com */
//                'subject' => $subject,
//                'from_email' => $from,
//                'from_name' => $fromName,
                'to' => array(
                    array(
                        'email' => $toEmail,
                        'name' => $toEmail,
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => $replyTo),
                'important' => false,
                'merge' => true,
                'merge_language' => 'handlebars',
                'global_merge_vars' => $mergeVars,
            );
            if (NULL != $addBcc) {
                $message['bcc_address'] = $addBcc;
            }
            $template_content = [];
            $response = $mandrill->messages->sendTemplate($templateName, $template_content, $message);
            if ($response)
                return true;
            else
                return false;
        } catch (Exception $e) {
            Log::error(__METHOD__ . $e->getMessage() . "|" . $e->getLine());
        }
    }
}
