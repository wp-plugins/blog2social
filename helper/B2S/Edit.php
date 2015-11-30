<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Tools.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Util.php';

$actions = array('publishDeleteNetwork', 'schedDeleteNetwork', 'schedChangeNetwork', 'schedPublishNetwork', 'showSchedNetwork');

if (!in_array(trim($_POST['action']), $actions) || empty($_POST['blog_user_id']) || (int) $_POST['blog_user_id'] == 0) {
    echo json_encode(array('result' => 0, 'data' => '', 'error' => 'NO_DATA'));
    exit;
} else {
    call_user_func($_POST['action']);
}

function showSchedNetwork() {
    global $wpdb;
    $isAdmin = (isset($_POST['isAdmin'])) ? (int) $_POST['isAdmin'] : 0;

    $sqlGetSchedNetworkList = $wpdb->prepare("SELECT `publishData` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `id` = %s AND `sched_network_date` != %s", (int) $_POST['blog_user_id'], (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    if ($isAdmin == 1) {
        $sqlGetSchedNetworkList = $wpdb->prepare("SELECT `publishData` FROM `b2s_filter` WHERE `id` = %s AND `sched_network_date` != %s", (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    }
    $resultSchedNetworkList = $wpdb->get_var($sqlGetSchedNetworkList);
    if (!empty($resultSchedNetworkList) && $resultSchedNetworkList != NULL) {
        $imageUrl= plugins_url('../assets/images/portale', dirname(__FILE__));
        $lang = (isset($_POST['lang'])) ? $_POST['lang'] : 'de';
        $schedNetworkListData = B2SUtil::getNetworkListByCheck(unserialize(stripslashes($resultSchedNetworkList)),$imageUrl,$lang);
        if ($schedNetworkListData !== false) {
            echo json_encode(array('result' => 1, 'data' => $schedNetworkListData, 'error' => ''));
            exit;
        }
    }
    echo json_encode(array('result' => 0, 'data' => '', 'error' => 'NO_OWNER'));
    exit;
}

function publishDeleteNetwork() {
    global $wpdb;
    $isAdmin = (isset($_POST['isAdmin'])) ? (int) $_POST['isAdmin'] : 0;
    $sqlCheckPublishbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `id` = %s AND `last_network_publish_date` != %s", (int) $_POST['blog_user_id'], (int) $_POST['report_id'], "0000-00-00 00:00:00");
    if ($isAdmin == 1) {
        $sqlCheckPublishbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `id` = %s AND `last_network_publish_date` != %s", (int) $_POST['report_id'], "0000-00-00 00:00:00");
    }
    $isPublish = $wpdb->get_var($sqlCheckPublishbyUser);
    if ($isPublish != NULL && (int) $isPublish == (int) $_POST['report_id']) {
        $wpdb->delete('b2s_filter', array('id' => (int) $_POST['report_id']));
        echo json_encode(array('result' => 1, 'error' => ''));
        exit;
    } else {
        echo json_encode(array('result' => 0, 'error' => 'NO_OWNER'));
        exit;
    }
}

function schedDeleteNetwork() {
    global $wpdb;
    $isAdmin = (isset($_POST['isAdmin'])) ? (int) $_POST['isAdmin'] : 0;
    $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `id` = %s AND `sched_network_date` != %s", (int) $_POST['blog_user_id'], (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    if ($isAdmin == 1) {
        $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `id` = %s AND `sched_network_date` != %s", (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    }
    $isSched = $wpdb->get_var($sqlCheckSchedbyUser);
    if ($isSched != NULL && (int) $isSched == (int) $_POST['sched_id']) {
        $result = json_decode(B2STools::schedDeleteNetwork(trim($_POST['token']), (int) $isSched));
        if (is_object($result) && !empty($result) && isset($result->result)) {
            if ($result->result == 1) {
                $wpdb->delete('b2s_filter', array('id' => (int) $_POST['sched_id']));
                echo json_encode(array('result' => 1, 'error' => ''));
                exit;
            }
            echo json_encode(array('result' => 0, 'error' => $result->data));
            exit;
        }
    }
    echo json_encode(array('result' => 0, 'error' => 'NO_OWNER'));
    exit;
}

function schedChangeNetwork() {
    global $wpdb;
    $isAdmin = (isset($_POST['isAdmin'])) ? (int) $_POST['isAdmin'] : 0;
    $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `id` = %s AND `sched_network_date` != %s", (int) $_POST['blog_user_id'], (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    if ($isAdmin == 1) {
        $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `id` = %s AND `sched_network_date` != %s", (int) $_POST['sched_id'], "0000-00-00 00:00:00");
    }
    $isSched = $wpdb->get_var($sqlCheckSchedbyUser);
    if ($isSched != NULL && (int) $isSched == (int) $_POST['sched_id']) {
        $user_date = (!isset($_POST['user_date']) || (int) $_POST['user_date'] == 0) ? time() : trim($_POST['user_date']);
        $result = json_decode(B2STools::schedChangeNetwork(trim($_POST['token']), (int) $isSched, B2SUtil::getCustomDate(trim($_POST['user_sched_date'] . ':00'), 'Y-m-d H:i:s'), $user_date));
        if (is_object($result) && !empty($result) && isset($result->result)) {
            if ($result->result == 1) {
                $wpdb->update('b2s_filter', array('sched_network_date' => B2SUtil::getCustomDate(trim($_POST['user_sched_date'] . ':00'), 'Y-m-d H:i:s')), array('id' => (int) $_POST['sched_id']));
                echo json_encode(array('result' => 1, 'error' => ''));
                exit;
            }
            echo json_encode(array('result' => 0, 'error' => $result->data));
            exit;
        }
    }
    echo json_encode(array('result' => 0, 'error' => 'NO_OWNER'));
    exit;
}

function schedPublishNetwork() {
    global $wpdb;

    if (!empty($_POST['token']) && isset($_POST['token'])) {
        $isAdmin = (isset($_POST['isAdmin'])) ? (int) $_POST['isAdmin'] : 0;
        $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `id` = %s AND `sched_network_date` != %s", (int) $_POST['blog_user_id'], (int) $_POST['sched_id'], "0000-00-00 00:00:00");
        if ($isAdmin == 1) {
            $sqlCheckSchedbyUser = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `id` = %s AND `sched_network_date` != %s", (int) $_POST['sched_id'], "0000-00-00 00:00:00");
        }
        $currentDateTime = date('Y-m-d H:i:s', current_time('timestamp'));
        $_POST['date'] = $currentDateTime;
        $isSched = $wpdb->get_var($sqlCheckSchedbyUser);
        if ($isSched != NULL && (int) $isSched == (int) $_POST['sched_id']) {
            $result = json_decode(B2STools::schedPublishNetwork($_POST));
            if (is_object($result) && !empty($result) && isset($result->result)) {
                if ((int) $result->result == 1) {
                    $wpdb->update('b2s_filter', array('sched_network_date' => '0000-00-00 00:00:00', 'last_network_publish_date' => $currentDateTime, 'publishData' => addslashes($result->data)), array('id' => (int) $isSched));
                    echo json_encode(array('result' => 1, 'network_id' => (int) $isSched, 'error' => ''));
                    exit;
                }
                echo json_encode(array('result' => 0, 'network_id' => 0, 'error' => $result->data));
                exit;
            }
        } else {
            echo json_encode(array('result' => 0, 'network_id' => 0, 'error' => 'NO_OWNER'));
            exit;
        }
    }
    echo json_encode(array('result' => 0, 'network_id' => 0, 'error' => 'NO_DATA'));
    exit;
}
