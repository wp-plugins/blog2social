<?php

/**
 * Plugin Name: Blog2Social
 * Plugin URI: http://www.blog2social.com
 * Description: Multi Channel Social Media Distribution Tool - Publish Your Post on your Social Media Accounts
 * Version: 1.0
 * Author: Thomas Kubik
 */
define('PLUGINVERS', '3.0');

register_activation_hook(__FILE__, 'activate_PRG_Connect');

register_deactivation_hook(__FILE__, 'deactivate_PRG_Connect');

add_action('admin_menu', 'PRGConnectMenu');
add_action('init', 'startSession');
add_action('init', 'updateDB');
add_action('admin_init', 'PRGConnect_init');
add_action('post_submitbox_misc_actions', 'publish_box');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function setFilterSent($userId, array $setter) {

    global $wpdb;
    $filterSent = serialize($setter);
    $sqlUpdateFilter = $wpdb->prepare("UPDATE `prg_connect_config` SET `filterSent` = %s WHERE `author_id` = %d", $filterSent, $userId);
    $wpdb->query($sqlUpdateFilter);
}

function getFilterSent($userId) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT `filterSent` FROM `prg_connect_config` WHERE `author_id` = %d", $userId);
    return unserialize($wpdb->get_var($sql));
}

function activate_PRG_Connect() {
    global $wpdb;
    $sqlCreateFirst = "CREATE TABLE IF NOT EXISTS `prg_connect_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wpid` bigint(20) unsigned DEFAULT NULL,
  `prgsent` tinyint(1) DEFAULT NULL,
  `twittersent` tinyint(1) DEFAULT NULL,
  `facebooksent` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlCreateFirst);

    $langWP = get_locale();
    $lang = 'en';
    if ($langWP == 'de_DE') {
        $lang = 'de';
    }

    $sqlCreateSecond = "CREATE TABLE IF NOT EXISTS `prg_connect_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `presse` varchar(64) NOT NULL,
  `fname` varchar(64) NOT NULL,
  `lname` varchar(64) NOT NULL,
  `address` varchar(64) NOT NULL,
  `pc` char(5) NOT NULL,
  `city` varchar(64) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `www` varchar(64) NOT NULL,
  `filterSent` varchar(255) NOT NULL,
  `UserBlogToken` varchar(255) NOT NULL,
  `lang` varchar(5) NOT NULL DEFAULT '$lang',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlCreateSecond);
}

function deactivate_PRG_Connect() {
    global $wpdb;
    $sqlDeleteFirst = 'DROP TABLE IF EXISTS `prg_connect_sent`';
    $wpdb->query($sqlDeleteFirst);
    $sqlDeleteSecond = 'DROP TABLE IF EXISTS `prg_connect_config`';
    $wpdb->query($sqlDeleteSecond);
}

function PRGConnectMenu() {
    global $wpdb;
    $currentUserID = get_current_user_id();
    $sql = $wpdb->prepare("SELECT * FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
    $userExist = $wpdb->get_row($sql);
    if (isset($_GET['prgPluginExtern'])) {
        switch ($_GET['prgPluginExtern']) {
            case 'sendSocialNetworks':
                require_once 'View/sendSocialNetworks.php';
                break;
            case 'sendMessage':
                require_once 'Controller/sendMessage.php';
                break;
            case 'Login':
                require_once 'View/Login.php';
                break;
            case 'sendPress':
                require_once 'View/sendPress.php';
                break;
            case 'facebookPreview' :
                require_once 'View/facebookPreview.php';
                break;
            case 'publishOnSN';
                require_once 'Helper/sendToSocials.php';
                break;
        }
        exit;
    }

    $pages = array();
    add_menu_page('Blog2Social', 'Blog2Social', 'read', 'blog2social', null, plugins_url('/images/logo16x.png', __FILE__));
    $pages[] = add_submenu_page('blog2social', 'Blog2Social', 'Dashboard', 'read', 'blog2social', 'blog2social');
    
    if ($userExist == NULL) {

        require_once 'Helper/getToken.php';

        $result = json_decode(GetNewToken::tokenFromPRG($currentUserID, get_option('home')));

        if ($result->error == '0') {
            $sqlInsertUserBlogToken = $wpdb->prepare("INSERT INTO `prg_connect_config` (`author_id`, `filterSent`, `UserBlogToken`) VALUES (%d, 'a:1:{s:3:\"prg\";b:0;}', %s);", $currentUserID, $result->token);
            $wpdb->query($sqlInsertUserBlogToken);
        }
    } elseif(substr($userExist->UserBlogToken,0 ,3) != 'v2_') {
        require_once 'Helper/getToken.php';

        $result = json_decode(GetNewToken::tokenFromPRG($currentUserID, get_option('home')));
        
        if($result->error == '0') {
            $sqlUpdateUserBlogToken = $wpdb->prepare("UPDATE `prg_connect_config` SET `UserBlogToken` = %s WHERE `author_id` = '$currentUserID'", $result->token);
            $wpdb->query($sqlUpdateUserBlogToken);
        }
    }

    $textAll = parse_ini_file('languages/lang.ini', TRUE);
    $text = $textAll[!empty($userExist->lang) ? $userExist->lang : 'en'];

    $pages[] = add_submenu_page('blog2social', $text['SN'], $text['SN'], 'read', 'prgconfigsocial', 'prgconfigsocial');
    $pages[] = add_submenu_page('blog2social', $text['SETTINGS'], $text['SETTINGS'], 'read', 'prgpluginconfig', 'prgpluginconfig');

    if (isset($_SESSION['prg_id'])) {
        add_submenu_page('blog2social', 'Logout', 'Logout', 'read', 'prglogout', 'prglogout');
    }

    foreach ($pages as $var) {
        add_action($var, 'addStylesnScripts');
    }
}

if ($_SERVER['PHP_SELF'] == '/wp-admin/post-new.php' || $_SERVER['PHP_SELF'] == '/wp-admin/post.php') {
    wp_enqueue_script('PRGNewPostJS', plugins_url('/js/b2s_NewPost.1_0.js', __FILE__));
    wp_enqueue_script('FancyBox', plugins_url('/js/jquery.fancybox-1_3_4.pack.js', __FILE__));
    wp_enqueue_style('FancyBoxStyle', plugins_url('/css/jquery.fancybox-1_3_4.css', __FILE__));
}

function addStylesnScripts() {
    wp_enqueue_style('PRGStyle');
    wp_enqueue_style('FancyBoxStyle');
    wp_enqueue_style('Bootstrap');
//    wp_enqueue_script('JQuery');
    wp_enqueue_script('FancyBox');
    wp_enqueue_script('PRGJS');
    wp_enqueue_script('PRGValidate');
    wp_enqueue_script('Bootstrap_full');
}

function blog2social() {
    require_once 'View/listPosts.php';
}

function prgconfigsocial() {
    require_once 'View/configsocial.php';
}

function prgpluginconfig() {
    require_once 'View/configPlugin.php';
}

function prglogout() {
    session_destroy();
    echo '<meta http-equiv="refresh" content="0; URL=?page=blog2social">';
}

function startSession() {
    if (!session_id()) {
        session_start();
    }
}

function updateDB() {

    global $wpdb;

    $checkTable = $wpdb->get_results("SHOW COLUMNS FROM prg_connect_config");

    foreach ($checkTable as $row) {
        $colsConfig[] = $row->Field;
    }

    if (!in_array('lang', $colsConfig)) {
        $langWP = get_locale();
        $lang = 'en';
        if ($langWP == 'de_DE') {
            $lang = 'de';
        }
        $sqlUpdateTableLang = $wpdb->prepare("ALTER TABLE `prg_connect_config` ADD `lang` varchar(5) NOT NULL DEFAULT %s", $lang);
        $wpdb->query($sqlUpdateTableLang);
    }

    if (!in_array('anrede', $colsConfig)) {
        $wpdb->query('ALTER TABLE `prg_connect_config` ADD `anrede` BOOLEAN NOT NULL AFTER `presse`;');
    }

    if (!in_array('nummer', $colsConfig)) {
        $wpdb->query('ALTER TABLE `prg_connect_config` ADD `nummer` VARCHAR(15) NOT NULL AFTER `address`;');
    }

    if (!in_array('land', $colsConfig)) {
        $wpdb->query('ALTER TABLE `prg_connect_config` ADD `land` VARCHAR(5) NOT NULL AFTER `city`;');
    }

    $field_array = $wpdb->get_results("SHOW COLUMNS FROM prg_connect_sent");

    foreach ($field_array as $row) {
        $columns[] = $row->Field;
    }

    if (!in_array('linkedinsent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `linkedinsent` tinyint(1) DEFAULT NULL');
    }
    if (!in_array('googleplussent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `googleplussent` tinyint(1) DEFAULT NULL');
    }
    if (!in_array('tumblrsent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `tumblrsent` tinyint(1) DEFAULT NULL');
    }
    if (!in_array('storifysent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `storifysent` tinyint(1) DEFAULT NULL');
    }
    if (!in_array('pinterestsent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `pinterestsent` tinyint(1) DEFAULT NULL');
    }
    if (!in_array('flickrsent', $columns)) {
        $wpdb->query('ALTER TABLE `prg_connect_sent` ADD `flickrsent` tinyint(1) DEFAULT NULL');
    }
}

function PRGConnect_init() {

    wp_register_style('PRGStyle', plugins_url('/css/b2s.1_0.css', __FILE__));
    wp_register_script('FancyBox', plugins_url('/js/jquery.fancybox-1_3_4.pack.js', __FILE__));
    wp_register_style('FancyBoxStyle', plugins_url('/css/jquery.fancybox-1_3_4.css', __FILE__));
    wp_register_script('PRGJS', plugins_url('/js/b2s.1_0.js', __FILE__));
    wp_register_script('PRGValidate', plugins_url('/js/jquery.validate.min.1_9.js', __FILE__));
    wp_register_style('Bootstrap', plugins_url('/css/bootstrap-modified.1_0.css', __FILE__));
    wp_register_script('Bootstrap_full', plugins_url('/js/bootstrap.min.3_0_3.js', __FILE__));
}

function publish_box() {
    global $wpdb;
    $currentUserID = get_current_user_id();
    $sql = $wpdb->prepare("SELECT `lang` FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
    $userExist = $wpdb->get_row($sql);

    $textAll = parse_ini_file('languages/lang.ini', TRUE);
    $text = $textAll[$userExist->lang ? $userExist->lang : 'en'];

    echo '<div class="misc-pub-section"><span style="padding: 2px 0 1px 20px; background: url(\'' . plugins_url('/images/logo16x.png', __FILE__) . '\') no-repeat left center;"><a href="#" class="openPRGCSocialMediaPoster">' . $text['SEND_SN'] . '</a></span></div>';
}
