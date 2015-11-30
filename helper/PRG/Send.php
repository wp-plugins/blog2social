<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Tools.php';
$actions = array('createMessagePRG');

if (!in_array(trim($_POST['action']), $actions) || empty($_POST['token'])) {
    echo json_encode(array('result' => 0, 'mandant' => 0));
    exit;
} else {
    call_user_func($_POST['action']);
}

function createMessagePRG() {
    if (empty($_POST) || (!isset($_GET['blog_user_id']) && (int) $_GET['blog_user_id'] == 0) || (!isset($_GET['post_id']) && (int) $_GET['post_id'] == 0)) {
        echo json_encode(array('result' => 0, 'create' => 0));
        exit;
    }

    if (isset($_POST['publish'])) {
        $_POST['status'] = (((int) $_POST['publish'] == 1) ? 'hold' : 'open');
    } else {
        $_POST['status'] = 'open';
    }

    if (!empty($_POST['bild'])) {
        if (class_exists('CurlFile')) {
            $_POST['bild'] = new CurlFile($_POST['bild'], 'image/jpg');
        } else {
            $_POST['bild'] = '@' . $_POST['bild'];
        }
    } else {
        $_POST['bild'] = "";
    }

    $result = json_decode(B2STools::createMessagePRG($_POST));

    if (is_object($result) && !empty($result) && isset($result->result) && (int) $result->result == 1 && isset($result->create) && (int) $result->create == 1) {

        global $wpdb;

        //Filter
        $sqlCheckPost = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `post_id` = %s", $_GET['post_id']);
        $postEntry = $wpdb->get_var($sqlCheckPost);

        if (!$postEntry) {
            $wpdb->insert('b2s_filter', array('last_prg_publish_date' => date('Y-m-d H:i:s'),'post_id'=> (int) $_GET['post_id'], 'blog_user_id' => (int) $_GET['blog_user_id']));
        } else {
            $wpdb->update('b2s_filter', array('last_prg_publish_date' => date('Y-m-d H:i:s')), array('post_id' => (int) $_GET['post_id'],'blog_user_id' => (int) $_GET['blog_user_id']));
        }

        //Contact
        $sqlCheckUser = $wpdb->prepare("SELECT `id` FROM `b2s_user_contact` WHERE `blog_user_id` = %d", $_GET['blog_user_id']);
        $userEntry = $wpdb->get_var($sqlCheckUser);

        $userContact = array('name_mandant' => strip_tags($_POST['name_mandant']),
            'created' => date('Y-m-d H:i;s'),
            'name_presse' => strip_tags($_POST['name_presse']),
            'anrede_presse' => strip_tags($_POST['anrede_presse']),
            'vorname_presse' => strip_tags($_POST['vorname_presse']),
            'nachname_presse' => strip_tags($_POST['nachname_presse']),
            'strasse_presse' => strip_tags($_POST['strasse_presse']),
            'nummer_presse' => strip_tags($_POST['nummer_presse']),
            'plz_presse' => strip_tags($_POST['plz_presse']),
            'ort_presse' => strip_tags($_POST['ort_presse']),
            'land_presse' => strip_tags($_POST['land_presse']),
            'email_presse' => strip_tags($_POST['email_presse']),
            'telefon_presse' => strip_tags($_POST['telefon_presse']),
            'fax_presse' => strip_tags($_POST['fax_presse']),
            'url_presse' => strip_tags($_POST['url_presse'])
        );

        if (!$userEntry) {
            $insertData = array_merge(array('blog_user_id' => (int) $_GET['blog_user_id']), $userContact);
            $wpdb->insert('b2s_user_contact', $insertData);
        } else {
            $wpdb->update('b2s_user_contact', $userContact, array('blog_user_id' => (int) $_GET['blog_user_id']));
        }

        echo json_encode(array('result' => 1, 'create' => 1));
        exit;
    }
    echo json_encode(array('result' => 0, 'create' => 0));
    exit;
}
