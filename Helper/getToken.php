<?php

class GetNewToken {

    public static function tokenFromPRG($id, $url) {
        if ($id == '' || $id == null) {
            return false;
        }

        $post = array(
            'id' => $id,
            'url' => $url,
            'action' => 'getKeySocialMedia',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://developer.pr-gateway.de/wp/auth.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        return $result;
    }

}