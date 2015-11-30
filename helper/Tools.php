<?php

class B2STools {

    public static function getToken($blog_user_id = 0, $firstName = '', $lastName = '', $email = '', $blog_url = '') {
        if (empty($blog_user_id) || empty($blog_url)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'getToken', 'blog_user_id' => $blog_user_id, 'blog_url' => $blog_url, 'firstname' => $firstName, 'lastname' => $lastName, 'email' => $email);

        return self::curlPost($url, $post);
    }

    public static function getPortale($token = '', $portal_id = 0, $mandant_id = 0) {

        if (empty($token)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'getPortale', 'token' => $token, 'portal_id' => $portal_id, 'mandant_id' => $mandant_id);

        return self::curlPost($url, $post);
    }

    public static function getInfo($token = '',$date='0000-00-00',$type = '') {

        if (empty($token)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'getUserInfo', 'token' => $token,'date' => $date, 'type' => $type);

        return self::curlPost($url, $post);
    }

    public static function getSchedInfo($token) {
        if (empty($token)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'getSchedInfo', 'token' => $token);

        return self::curlPost($url, $post);
    }

    public static function getMandanten($token = '') {

        if (empty($token)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'getMandant', 'token' => $token);

        return self::curlPost($url, $post);
    }

    public static function schedDeleteNetwork($token = '', $sched_id = 0) {
        if (empty($token)) {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'schedDeleteNetwork', 'token' => $token, 'sched_id' => (int) $sched_id);

        return self::curlPost($url, $post);
    }

    public static function schedChangeNetwork($token = '', $sched_id = 0, $user_sched_date = "0000-00-00 00:00:00", $user_date = "0000-00-00 00:00:00") {
        if (empty($token) || $user_sched_date == '0000-00-00 00:00:00') {
            return false;
        }
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'schedChangeNetwork', 'token' => $token, 'sched_id' => (int) $sched_id, 'user_sched_date' => $user_sched_date, 'user_date' => $user_date);

        return self::curlPost($url, $post);
    }

    public static function addMandant($token, $name = '') {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'addMandant', 'token' => $token, 'name' => strip_tags($name));

        return self::curlPost($url, $post);
    }

    public static function deleteMandant($token, $mandant_id = 0) {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'deleteMandant', 'token' => $token, 'mandant_id' => (int) $mandant_id);

        return self::curlPost($url, $post);
    }

    public static function checkKey($token, $key) {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'checkKey', 'token' => $token, 'key' => $key);
        return self::curlPost($url, $post);
    }

    public static function getPRGCategory() {
        $url = 'http://developer.pr-gateway.de/wp/v2/get.php?action=getCategory';
        return self::curlGet($url);
    }

    public static function getPRGCountry() {
        $url = 'http://developer.pr-gateway.de/wp/v2/get.php?action=getCountry';
        return self::curlGet($url);
    }

    public static function createMessagePRG($datas = array()) {
        $url = 'http://developer.pr-gateway.de/wp/v2/post.php';
        return self::curlPost($url, $datas);
    }

    public static function sentToNetwork($postData= array(),$publishData = array(),$schedData = array()) {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        return self::curlPost($url, array('action' => 'sentToNetwork','postData' => serialize($postData), 'publishData' => serialize($publishData), 'schedData' => serialize($schedData)));
    }

    public static function schedPublishNetwork($datas = array()) {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        return self::curlPost($url, array('action' => 'schedPublishNetwork', 'data' => serialize($datas)));
    }

    public static function deleteNetwork($token, $portal_id = 0, $mandant_id = 0) {
        $url = 'https://developer.blog2social.com/wp/v2/post.php';
        $post = array('action' => 'deleteNetwork', 'token' => $token, 'portal_id' => (int) $portal_id, 'mandant_id' => (int) $mandant_id);
        return self::curlPost($url, $post);
    }

    public static function loginPRG($username, $password) {
        $loginUrl = 'http://developer.pr-gateway.de/wp/v2/auth.php';
        $pubKey = json_decode(self::curlGet($loginUrl . '?publicKey=true', array()));

        if (!empty($pubKey) && is_object($pubKey) && isset($pubKey->publicKey) && !empty($pubKey->publicKey) && function_exists('openssl_public_encrypt')) {
            $usernameCrypted = '';
            $passwordCrypted = '';
            openssl_public_encrypt(trim($username), $usernameCrypted, $pubKey->publicKey);
            openssl_public_encrypt(trim($password), $passwordCrypted, $pubKey->publicKey);

            $datas = array(
                'action' => 'loginPRG',
                'username' => base64_encode($usernameCrypted),
                'password' => base64_encode($passwordCrypted),
            );
            $result = json_decode(trim(self::curlPost($loginUrl, $datas)));

            if (!empty($result) && is_object($result) && isset($result->prg_token) && !empty($result->prg_token) && isset($result->prg_id) && !empty($result->prg_id)) {
                if ((int) $result->prg_id > 0) {
                    $_SESSION['b2s_prg_id'] = $result->prg_id;
                    $_SESSION['b2s_prg_token'] = $result->prg_token;
                    return array('result' => 1, 'b2s_prg_id' => $result->prg_id, 'b2s_prg_token' => $result->prg_token);
                }
            }
            return array('result' => 0, 'b2s_prg_id' => 0, 'b2s_prg_token' => 0);
        }

        return array('result' => 2, 'b2s_prg_id' => 0, 'b2s_prg_token' => 0); // Error OPENSSL nicht vorhanden!
    }

    public static function curlPost($url = '', $post = array()) {
        if (empty($url) || empty($post)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($ch);
        return $result;
    }

    public static function curlGet($url = '', $header = array("Content-Type:application/x-www-form-urlencoded")) {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        return $result;
    }

}
