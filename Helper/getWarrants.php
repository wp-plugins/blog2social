<?php
set_time_limit(60);

class WarrantsCheck {

    public static function verifyCreds($id = null) {
        if ($id == '' || $id == null) {
            return false;
        }

        $post = array(
            'action' => 'getCreds',
            'userid' => $id,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://developer.pr-gateway.de/wp/index.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        return json_decode($result);
    }

}
