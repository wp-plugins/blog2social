<?php
session_start();
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Tools.php';
$actions = array('addMandant', 'deleteMandant', 'createMessagePRG', 'loginPRG', 'logoutPRG', 'deleteNetwork', 'checkKey');
if (!in_array(trim($_POST['action']), $actions) || empty($_POST['token'])) {
    echo json_encode(array('result' => 0, 'mandant' => 0));
    exit;
} else {
    if (isset($_POST['action']) && !empty($_POST['action'])) {
        call_user_func($_POST['action']);
    }
}
function logoutPRG() {
    unset($_SESSION['b2s_prg_token']);
    unset($_SESSION['b2s_prg_id']);
    echo json_encode(array('result' => 1));
    exit;
}
function deleteNetwork() {
    if (!isset($_POST['portal_id']) || !isset($_POST['mandant_id']) || (int) $_POST['portal_id'] == 0) {
        echo json_encode(array('result' => 0));
        exit;
    }
    echo B2STools::deleteNetwork($_POST['token'], $_POST['portal_id'], $_POST['mandant_id']);
}
function addMandant() {
    if (empty($_POST['name']) || empty($_POST['token'])) {
        echo json_encode(array('result' => 0, 'mandant' => 0));
        exit;
    }
    echo B2STools::addMandant($_POST['token'], $_POST['name']);
}
function deleteMandant() {
    if ((int) $_POST['mandant_id'] == 0 || empty($_POST['token'])) {
        echo json_encode(array('result' => 0, 'mandant' => 0));
        exit;
    }
    echo B2STools::deleteMandant($_POST['token'], (int) $_POST['mandant_id']);
}
function checkKey() {
    if (empty($_POST['b2sLizenzKey']) || empty($_POST['token'])) {
        echo json_encode(array('result' => 0, 'mandant' => 0));
        exit;
    }
    echo B2STools::checkKey($_POST['token'], $_POST['b2sLizenzKey']);
}
function loginPRG() {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo json_encode(array('result' => 0));
        exit;
    }
    $result = B2STools::loginPRG(trim($_POST['username']), trim($_POST['password']));
    if ($result !== false && is_array($result) && isset($result['result'])) {
        echo json_encode($result);
        exit;
    }
    echo json_encode(array('result' => 0));
    exit;
}