<?php

if (!function_exists('add_action')) {
    exit;
}

if (empty($_POST)) {
    return false;
}

$url = 'https://developer.pr-gateway.de/wp/post.php';

$postToPRG = array(
    'kategorie_id' => $_POST['kategorie_id'],
    'user_id' => $_POST['user_id'],
    'title' => $_POST['title'],
    'status' => $_POST['publish'] == '1' ? 'publish' : 'draft',
    'sprache' => $_POST['sprache'],
    'subline' => $_POST['subline'],
    'message' => $_POST['message'],
    'keywords' => $_POST['keywords'],
    'shorttext' => $_POST['shorttext'],
    'video_link' => $_POST['video_link'],
    'bildtitel' => $_POST['bildtitel'],
    'bildcopyright' => $_POST['bildcopyright'],
    'name_mandant' => $_POST['name_mandant'],
    'anrede_mandant' => $_POST['anrede_mandant'],
    'vorname_mandant' => $_POST['vorname_mandant'],
    'nachname_mandant' => $_POST['nachname_mandant'],
    'email_mandant' => $_POST['email_mandant'],
    'plz_mandant' => $_POST['plz_mandant'],
    'ort_mandant' => $_POST['ort_mandant'],
    'land_mandant' => $_POST['land_mandant'],
    'strasse_mandant' => $_POST['strasse_mandant'],
    'nummer_mandant' => $_POST['nummer_mandant'],
    'telefon_mandant' => $_POST['telefon_mandant'],
    'url_mandant' => $_POST['url_mandant'],
    'info_mandant' => $_POST['info_mandant'],
    'name_presse' => $_POST['name_presse'],
    'anrede_presse' => $_POST['anrede_presse'],
    'vorname_presse' => $_POST['vorname_presse'],
    'nachname_presse' => $_POST['nachname_presse'],
    'strasse_presse' => $_POST['strasse_presse'],
    'nummer_presse' => $_POST['nummer_presse'],
    'plz_presse' => $_POST['plz_presse'],
    'ort_presse' => $_POST['ort_presse'],
    'land_presse' => $_POST['land_presse'],
    'email_presse' => $_POST['email_presse'],
    'telefon_presse' => $_POST['telefon_presse'],
    'fax_presse' => '',
    'url_presse' => $_POST['url_presse'],
    'action' => 'createMessagePRG',
);

if (!empty($_POST['bild'])) {
    if (class_exists('CurlFile')) {
        $postToPRG['bild'] = new CurlFile($_POST['bild'], 'image/jpg');
    } else {
        $postToPRG['bild'] = '@' . $_POST['bild'];
    }
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postToPRG);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$result = curl_exec($ch);
curl_close($ch);

global $wpdb;

$resultObj = json_decode($result);

if (isset($resultObj->success) && $resultObj->success == 'true') {
    $sqlCheckIsset = $wpdb->prepare("SELECT `id` FROM `prg_connect_sent` WHERE `wpid` = %d", $_POST['blogid']);
    $issetVar = $wpdb->get_var($sqlCheckIsset);
    if (!$issetVar) {
        $wpdb->insert('prg_connect_sent', array('wpid' => $_POST['blogid'], 'prgsent' => 1));
    } else {
        $wpdb->update('prg_connect_sent', array('wpid' => $_POST['blogid'], 'prgsent' => 1), array('wpid' => $_POST['blogid']));
    }
}

$sqlSaveUser = $wpdb->prepare("UPDATE `prg_connect_config`
				SET
				`presse` = %s,
                                `anrede` = %d,
				`fname` = %s,
				`lname` = %s,
				`address` = %s,
                                `nummer` = %s,
				`pc` = %s,
				`city` = %s,
                                `land` = %s,
				`phone` = %s,
				`email` = %s,
				`www` = %s
				WHERE `author_id` = %d", $_POST['name_presse'], $_POST['anrede_presse'], $_POST['vorname_presse'], $_POST['nachname_presse'], $_POST['strasse_presse'], $_POST['nummer_presse'], $_POST['plz_presse'], $_POST['ort_presse'], $_POST['land_presse'], $_POST['telefon_presse'], $_POST['email_presse'], $_POST['url_presse'], $currentUserID);

$wpdb->query($sqlSaveUser);

echo $result;
