<?php

if (!function_exists('add_action')) {
    exit;
}

set_time_limit(60);

global $wpdb;
$currentUserID = get_current_user_id();

$sql = $wpdb->prepare("SELECT * FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
$userExist = $wpdb->get_row($sql);

$post = 'userid=' . substr($userExist->UserBlogToken, 3) . '&action=update&vers=' . PLUGINVERS;

$postData = array_map('stripslashes_deep', $_POST);

if (isset($postData['checkFacebook']) && $postData['checkFacebook'] == 'on') {
    $post .='&inputs%5B1%5D%5Blink%5D=' . urlencode($postData['shortUrl']) .
            '&inputs%5B1%5D%5Bpicture%5D=' . urlencode($postData['image']) .
            '&inputs%5B1%5D%5Bmessage%5D=' . urlencode($postData['prg_facebook']);
}

if (isset($postData['checkTwitter']) && $postData['checkTwitter'] == 'on') {
    $addUrl = $postData['urlForTwit'] == '' ? '' : ' ' . $postData['urlForTwit'];
    $post .= '&inputs%5B2%5D%5Bmessage%5D=' . urlencode($postData['prg_twitter'] . $addUrl);
}

if (isset($postData['checkLinkedin']) && $postData['checkLinkedin'] == 'on') {
    $post .= '&inputs%5B3%5D%5Bmessage%5D=' . urlencode($postData['prg_linkedin']) .
            '&inputs%5B3%5D%5Btitel%5D=' . urlencode($postData['postTitle']) .
            '&inputs%5B3%5D%5Bpicture%5D=' . urlencode($postData['image']) .
            '&inputs%5B3%5D%5Blink%5D=' . urlencode($postData['shortUrl']);
}

if (isset($postData['checkTumblr']) && $postData['checkTumblr'] == 'on') {

    $addToTumblrPost = '&inputs%5B4%5D%5Btags%5D=';
    foreach ($postData['prg_tumblr_tags'] as $tag) {
        $addToTumblrPost .= $tag.',';
    }

    $postData['prg_tumblr'] = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a href="$2">$3</a>', $postData['prg_tumblr']);

    $post .= '&inputs%5B4%5D%5Btitel%5D=' . urlencode($postData['postTitle']) .
            '&inputs%5B4%5D%5Blink%5D=' . urlencode($postData['shortUrl']) .
            '&inputs%5B4%5D%5Bmessage%5D=' . urlencode(nl2br($postData['prg_tumblr'])) .
            '&inputs%5B4%5D%5Bpicture%5D=' . urlencode($postData['image']) .
            $addToTumblrPost;
}

if (isset($postData['checkStorify']) && $postData['checkStorify'] == 'on') {
    $post .= '&inputs%5B5%5D%5Btitel%5D=' . urlencode($postData['postTitle']) .
            '&inputs%5B5%5D%5Blink%5D=' . urlencode($postData['shortUrl']) .
            '&inputs%5B5%5D%5Bmessage%5D=' . urlencode($postData['prg_storify']);
}

if (isset($postData['checkPinterest']) && $postData['checkPinterest'] == 'on') {
    $post .= '&inputs%5B6%5D%5Bmessage%5D=' . urlencode($postData['prg_pinterest']) .
            '&inputs%5B6%5D%5Bpicture%5D=' . urlencode($postData['image']) .
            '&inputs%5B6%5D%5Blink%5D=' . urlencode($postData['shortUrl']);
}

if (isset($postData['checkFlickr']) && $postData['checkFlickr'] == 'on') {
    $post .= '&inputs%5B7%5D%5Bpicture%5D=' . urlencode($postData['image']) .
            '&inputs%5B7%5D%5Btitel%5D=' . urlencode($postData['postTitle']) .
            '&inputs%5B7%5D%5Blink%5D=' . urlencode($postData['shortUrl']) .
            '&inputs%5B7%5D%5Bmessage%5D=' . urlencode($postData['prg_flickr']);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://developer.pr-gateway.de/wp/index.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

echo $result = curl_exec($ch);

$resultObj = json_decode($result);

$sqlCheckIsset = $wpdb->prepare("SELECT `id` FROM `prg_connect_sent` WHERE `wpid` = %d", $postData['blogid']);
$issetVar = $wpdb->get_var($sqlCheckIsset);
if (!$issetVar) {
    $dbAdd = 'insert';
} else {
    $dbAdd = 'update';
}

$portals = array(
    'DUMMY',
    'facebooksent',
    'twittersent',
    'linkedinsent',
    'tumblrsent',
    'storifysent',
    'pinterestsent',
    'flickrsent',
);

$changeArray = array(
    'wpid' => $postData['blogid'],
);

foreach ($resultObj as $key => $val) {
    if ($val == true) {
        $changeArray[$portals[$key]] = 1;
    }
}

if ($dbAdd == 'insert') {
    $wpdb->insert('prg_connect_sent', $changeArray);
} elseif ($dbAdd == 'update') {
    $wpdb->update('prg_connect_sent', $changeArray, array('wpid' => $postData['blogid']));
}