<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Tools.php';
$actions = array('sentToNetwork');
if (!in_array(trim($_POST['action']), $actions) || empty($_POST['token'])) {
    echo json_encode(array('result' => 0));
    exit;
} else {
    call_user_func($_POST['action']);
}
function sentToNetwork() {
    if (isset($_POST['mandant_id']) && !empty($_POST['token']) && isset($_POST['token']) && (int) $_POST['blog_user_id'] > 0 && (int) $_POST['post_id'] > 0) {
        $result = json_decode(B2STools::sentToNetwork($_POST));
        if (is_object($result) && !empty($result) && isset($result->result)) {
            if ($result->result == 1) {
                global $wpdb;
                //Filter
                $sqlCheckPost = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `post_id` = %s", trim($_POST['blog_user_id']), trim($_POST['post_id']));
                $postEntry = $wpdb->get_var($sqlCheckPost);
                $currentDateTime = current_time('mysql');
                if ($postEntry == NULL || (int)$postEntry == 0) {
                    $wpdb->insert('b2s_filter', array('last_network_publish_date' => $currentDateTime, 'publishData' => addslashes($result->data), 'post_id' => (int) $_POST['post_id'], 'blog_user_id' => (int) $_POST['blog_user_id']));
                    $postEntry = $wpdb->insert_id;
                } else {
                    $wpdb->update('b2s_filter', array('last_network_publish_date' => $currentDateTime, 'publishData' => addslashes($result->data)), array('post_id' => (int) $_POST['post_id'], 'blog_user_id' => (int) $_POST['blog_user_id']));
                }
                if ((int) $postEntry > 0) {
                    echo json_encode(array('result' => 1, 'network_id' => (int) $postEntry , 'error' => ''));
                    exit;
                }
            }
            echo json_encode(array('result' => 0,'network_id' =>0, 'error' => $result->data));
            exit;
        }
    }
    echo json_encode(array('result' => 0,'network_id' =>0, 'error' => 'NO_DATA'));
    exit;
}