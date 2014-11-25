<?php

session_start();

$url = 'https://developer.pr-gateway.de/wp/auth.php';
$post1 = array(
	'action' => 'getTokenPRG',
	'user' => $_POST['user'],
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$result1 = trim(curl_exec($ch));

if ($result1 == 'wrong') {
	echo json_encode(array('status' => false));
	exit;
}

$post2 = array(
	'action' => 'authPRG',
	'user' => $_POST['user'],
	'pass' => md5($_POST['pass'] . $result1) . ':' . $result1,
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $post2);

$result2 = trim(curl_exec($ch));

if ($result2 === false) {
	echo json_encode(array('status' => false));
	exit;
} else {
	echo json_encode(array('status' => $result2));
}

if (preg_match('%[0-9a-zA-Z]{6,}%', $result2)) {
	$_SESSION['prg_id'] = $result2;
}

