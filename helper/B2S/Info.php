<?php

$actions = array('hideInfoVersion');

if (!in_array(trim($_POST['action']), $actions) || empty($_POST['blog_user_id'])) {
    echo json_encode(array('result' => 0));
    exit;
} else {
    call_user_func($_POST['action']);
}

function hideInfoVersion() {
    if ((int) $_POST['blog_user_id'] > 0) {
        global $wpdb;
        $wpdb->update('b2s_user', array('feature' => 1), array('blog_user_id' => (int) $_POST['blog_user_id']));
        echo json_encode(array('result' => 1));
        exit;
    }
    echo json_encode(array('result' => 0));
    exit;
}
