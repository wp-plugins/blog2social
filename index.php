<?php
/**
 * Plugin Name: Blog2Social
 * Plugin URI: http://www.blog2social.de
 * Description: Multi Channel Social Media Distribution Tool - Publish Your Post on your Social Media Accounts
 * Version: 2.0.4
 * Author: Adenion Developer Team
 */
define('B2SVERSION', '2.0.4');
define('B2SPLUGINLANGUAGE', serialize(array('de_DE', 'en_US')));
register_activation_hook(__FILE__, 'b2sActivate');
//register_deactivation_hook(__FILE__, 'b2sDeactivate');
add_action('init', 'b2sSession');
add_action('admin_init', 'b2sInit');
add_action('admin_menu', 'b2sAdminMenu');
add_action('post_submitbox_misc_actions', 'b2sButton');
function b2sActivate() {
    global $wpdb;
    $sqlDeleteFirst = 'DROP TABLE IF EXISTS `prg_connect_sent`';
    $wpdb->query($sqlDeleteFirst);
    $sqlDeleteSecond = 'DROP TABLE IF EXISTS `prg_connect_config`';
    $wpdb->query($sqlDeleteSecond);
    $sqlCreateUserKey = "CREATE TABLE IF NOT EXISTS `b2s_user` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `token` varchar(255) NOT NULL,
                            `blog_user_id` int(11) NOT NULL,
                            `feature` TINYINT(2) NOT NULL,
                            PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlCreateUserKey);
    $sqlCreateUserFilter = "CREATE TABLE IF NOT EXISTS `b2s_filter` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `blog_user_id` int(11) NOT NULL,
                            `post_id` int(11) NOT NULL,
                            `last_network_publish_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                            `last_prg_publish_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                            `publishData` TEXT NOT NULL, 
                            PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlCreateUserFilter);
    $wpdb->query("ALTER TABLE `b2s_filter` ADD INDEX(`post_id`);");
    $wpdb->query("UPDATE `b2s_user` SET `feature` = '0';");  
    $sqlCreateUserContact = " CREATE TABLE IF NOT EXISTS `b2s_user_contact`(
                                `id` int(5) NOT  NULL  AUTO_INCREMENT ,
                                `blog_user_id` int(11)  NOT  NULL ,
                                `name_mandant` varchar(100)  NOT  NULL ,
                                `created` datetime NOT  NULL DEFAULT  '0000-00-00 00:00:00',
                                `name_presse` varchar(100)  NOT  NULL ,
                                `anrede_presse` enum('0','1','2')  NOT  NULL DEFAULT  '0' COMMENT  '0=Frau,1=Herr 2=keine Angabe',
                                `vorname_presse` varchar(50)  NOT  NULL ,
                                `nachname_presse` varchar(50)  NOT  NULL ,
                                `strasse_presse` varchar(100)  NOT  NULL ,
                                `nummer_presse` varchar(5)  NOT  NULL DEFAULT  '',
                                `plz_presse` varchar(10)  NOT  NULL ,
                                `ort_presse` varchar(75)  NOT  NULL ,
                                `land_presse` varchar(3)  NOT  NULL DEFAULT  'DE',
                                `email_presse` varchar(75)  NOT  NULL ,
                                `telefon_presse` varchar(30)  NOT  NULL ,
                                `fax_presse` varchar(30)  NOT  NULL ,
                                `url_presse` varchar(150)  NOT  NULL ,
                                PRIMARY  KEY (`id`) ,
                                KEY `blog_user_id`(`blog_user_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    $wpdb->query($sqlCreateUserContact);
}
/*function b2sDeactivate() {
    global $wpdb;
    $sqlDeleteFirst = 'DROP TABLE IF EXISTS `b2s_user`';
    $wpdb->query($sqlDeleteFirst);
    $sqlDeleteSecond = 'DROP TABLE IF EXISTS `b2s_filter`';
    $wpdb->query($sqlDeleteSecond);
    $sqlDeleteFirth = 'DROP TABLE IF EXISTS `b2s_user_contact`';
    $wpdb->query($sqlDeleteFirth);
}*/

function b2sAdminMenu() {
    $subPages = array();
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');

    if (isset($_GET['b2sConnect']) && !empty($_GET['b2sConnect'])) {
        switch ($_GET['b2sConnect']) {
            case 'sentPRG':
                require_once 'helper/PRG/Send.php';
                break;
            case 'sentB2S':
                require_once 'helper/B2S/Send.php';
                break;
            case 'b2sPlugin':
                require_once 'helper/B2S/Info.php';
                break;
            default :
                require_once 'helper/Error.php';
                break;
        }
    }
    add_menu_page($lang['PLUGINNAME'], $lang['PLUGINNAME'], 'read', strtolower($lang['PLUGINNAME']), null, plugins_url('/assets/images/b2s_icon.png', __FILE__));
    $subPages[] = add_submenu_page(strtolower($lang['PLUGINNAME']), strtolower($lang['PLUGINNAME']), $lang['MENU_CONTENT'], 'read', strtolower($lang['PLUGINNAME']), 'b2sContent');
    $subPages[] = add_submenu_page(strtolower($lang['PLUGINNAME']), $lang['MENU_NETWORKS'], $lang['MENU_NETWORKS'], 'read', $lang['MENU_NETWORKS_SLUG'], 'b2sNetwork');
    $subPages[] = add_submenu_page(null, $lang['MENU_CONTENT'], $lang['MENU_CONTENT'], 'read', $lang['MENU_PRG_LOGIN_SLUG'], 'loginPRG');
    $subPages[] = add_submenu_page(null, $lang['MENU_CONTENT'], $lang['MENU_CONTENT'], 'read', $lang['MENU_PRG_CONNECT_SLUG'], 'formPRG');
    $subPages[] = add_submenu_page(null, $lang['MENU_CONTENT'], $lang['MENU_CONTENT'], 'read', $lang['MENU_NETWORK_SHIP_SLUG'], 'b2sShip');
    $subPages[] = add_submenu_page(null, $lang['MENU_CONTENT'], $lang['MENU_CONTENT'], 'read', $lang['MENU_NETWORK_REPORT_SLUG'], 'b2sReport');
    foreach ($subPages as $var) {
        add_action($var, 'b2sAddAssets');
    }
    global $wpdb;
    $currentUserID = get_current_user_id();
    $sql = $wpdb->prepare("SELECT * FROM `b2s_user` WHERE `blog_user_id` = %d", $currentUserID);
    $userExist = $wpdb->get_row($sql);
    if (empty($userExist) && !isset($userExist->token)) {
        require_once plugin_dir_path(__FILE__) . 'helper/Tools.php';
        $userInfo = get_userdata($currentUserID);
        $result = json_decode(B2STools::getToken($currentUserID, $userInfo->first_name, $userInfo->last_name, $userInfo->user_email, get_option('home')));
        if (isset($result->result) && (int) $result->result == 1 && isset($result->token)) {
            //=String ,d= integer  
            $sqlInsertToken = $wpdb->prepare("INSERT INTO `b2s_user` (`token`, `blog_user_id`) VALUES (%s,%d);", $result->token, (int) $currentUserID);
            $wpdb->query($sqlInsertToken);
            define('B2STOKEN', $result->token);
        } else {
            $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
            $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
            define('B2SERRORDEFAULT', $lang['ERROR_DEFAULT']);
        }
    } else {
        define('B2STOKEN', $userExist->token);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://developer.blog2social.com/wp/v1/versionCheck.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $currentVersion = explode('#', curl_exec($ch));
    $b2sUpdate = ($currentVersion[0] != B2SVERSION) ? 1 : 0;
    define('B2SUPATE', $b2sUpdate);
}
function b2sAddAssets() {
    wp_enqueue_style('B2SCSS');
    wp_enqueue_script('B2SBOOTJS');
    wp_enqueue_script('B2SVALID');
}
function b2sSession() {
    if (!session_id()) {
        session_start();
    }
}
function b2sInit() {
    wp_register_style('B2SCSS', plugin_dir_url(__FILE__) . 'assets/css/b2s.css');
    wp_register_script('B2SJS', plugin_dir_url(__FILE__) . 'assets/js/b2s.js');
    wp_register_script('B2SSHIPJS', plugin_dir_url(__FILE__) . 'assets/js/b2s.ship.js');
    wp_register_style('PRGCSS', plugin_dir_url(__FILE__) . 'assets/css/prg.css');
    wp_register_script('PRGJS', plugin_dir_url(__FILE__) . 'assets/js/prg.js');
    wp_register_script('B2SCOUNTDOWNJS', plugin_dir_url(__FILE__) . 'assets/js/downCount.js');
    wp_register_script('B2SBOOTJS', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js');
    wp_register_script('B2SVALID', plugins_url('assets/js/jquery.validate.js', __FILE__));
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    define('B2SLANGUAGE', $language);
}
function b2sContent() {
    wp_enqueue_script('B2SJS');
    wp_enqueue_script('B2SCOUNTDOWNJS');
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'view/listPost.php';
}
function b2sNetwork() {
    wp_enqueue_script('B2SJS');
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'view/listNetwork.php';
}
function b2sShip() {
    wp_enqueue_script('B2SSHIPJS');
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'helper/B2S/Form.php';
}
function b2sReport() {
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'view/viewReport.php';
}
function loginPRG() {
    wp_enqueue_style('PRGCSS');
    wp_enqueue_script('PRGJS');
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'helper/PRG/Login.php';
}
function formPRG() {
    wp_enqueue_style('PRGCSS');
    wp_enqueue_script('PRGJS');
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    require_once 'helper/PRG/Form.php';
}
function b2sButton() {
    $language = (!in_array(get_locale(), unserialize(B2SPLUGINLANGUAGE))) ? 'en_US' : get_locale();
    $lang = parse_ini_file(plugin_dir_path(__FILE__) . 'languages/' . $language . '.ini');
    $postId = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($postId > 0) {
        echo '<div class="misc-pub-section"> <span style="padding: 2px 0 10px 25px; background: url(\'' . plugins_url('/assets/images/b2s_icon.png', __FILE__) . '\') no-repeat left center;"></span><a class="button button-primary button-small" href="?page=sentNetwork&postId=' . $postId . '">' . $lang['CUT_SHORT_BUTTON_PUBLISH'] . '</a></div>';
    } else {
        echo '<div class="misc-pub-section"> <span style="padding: 2px 0 10px 25px; background: url(\'' . plugins_url('/assets/images/b2s_icon.png', __FILE__) . '\') no-repeat left center;"></span><a class="button button-primary button-small"  onclick="jQuery(\'#publish\').trigger(\'click\');return false;" href="#">' . $lang['CUT_SHORT_BUTTON_SAVE'] . '</a></div>';
    }
}