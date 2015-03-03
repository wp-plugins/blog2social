<?php

session_start();

$urlFirst = 'http://developer.pr-gateway.de/wp/auth/prg/crypt.php?getPublicKey=true';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $urlFirst);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$publicKeyJson = curl_exec($ch);

$publicKey = json_decode($publicKeyJson);

$username = '';
$password = '';

openssl_public_encrypt($_POST['user'], $username, $publicKey->publickey);
openssl_public_encrypt($_POST['pass'], $password, $publicKey->publickey);

$urlSecond = 'http://developer.pr-gateway.de/wp/auth/prg/auth.php';

$postData = array(
    'action' => 'login',
    'user' => base64_encode($username),
    'pass' => base64_encode($password),
);

curl_setopt($ch, CURLOPT_URL, $urlSecond);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$resultJson = trim(curl_exec($ch));

$resultObj = json_decode($resultJson);

if ($resultObj->error != 0) {
    echo json_encode(array('status' => false));
} else {
    echo json_encode(array('status' => 'ok'));
    $_SESSION['prg_id'] = $resultObj->token;
}

