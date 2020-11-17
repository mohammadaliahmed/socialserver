<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendNotification extends Model
{
    public function sendPushNotification($fcm_token, $title, $message, $id, $type)
    {
        $push_notification_key = 'AAAAa8IOg6I:APA91bECmSeAYBGuAPiOTEPVOW01OaSUDfOkHlOTmnaoSIXRtCgJ8gG7_eNmDyWajUSPql2z_n_ZulqHlmpfmLEcwCE22giKKziePk3U7yGD4k3W5nNOMW0sVYAXln6g1i6cTr8FoXqa';
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "priority" : "high",
                
            "data" : {
                "Message" : "' . $message . '",
                "Type":"' . $type . '",
                "Title":"' . $title . '",
                "Username":"Salman",
                "Id" : "' . $id . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

}
