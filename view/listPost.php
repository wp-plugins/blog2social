<br>
<!-CountDown-->
<script>
    jQuery.noConflict();
    jQuery(document).ready(function () {
        jQuery('.countdown').downCount({
            date: jQuery('#nextSendDate').val(),
            offset: 0
        });
    });
</script>
<div class="col-md-12 col-xs-12">
    <div class="pull-left">
        <a target="_blank" href="http://service.blog2social.com">
            <img class="b2s-logo" src="<?php echo plugins_url('/assets/images/b2s_logo.png', dirname(__FILE__)); ?>">
        </a>
    </div>
    <div class="pull-right">
        <a href="http://service.blog2social.com" class="pull-right btn btn-success" target="_blank">Support</a>
        <button type="button" class="pull-right btn btn-danger" onclick="window.open('http://developer.blog2social.com/wp/v1/feedback/?lang=<?php echo substr(B2SLANGUAGE, 0, 2); ?>&token=<?php echo B2STOKEN; ?>', 'Blog2Social Feedback', 'width=650,height=520,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20');">
            <?php echo $lang['FEEDBACK_BUTTON']; ?>
        </button>
    </div>
</div>
<br class="clear">
<input type="hidden" name="lang" id="lang" value="<?php echo substr(B2SLANGUAGE, 0, 2); ?>">
<input type='hidden' id='token' value='<?php echo B2STOKEN; ?>'>
<input type='hidden' id='prg_token' value='<?php echo (isset($_SESSION['b2s_prg_token']) && !empty($_SESSION['b2s_prg_token'])) ? $_SESSION['b2s_prg_token'] : 0; ?>'>
<input type='hidden' id='prg_id' value='<?php echo (isset($_SESSION['b2s_prg_id']) && !empty($_SESSION['b2s_prg_id'])) ? $_SESSION['b2s_prg_id'] : 0; ?>'>
<input type='hidden' id='plugin_url' value='<?php echo plugins_url('', dirname(__FILE__)); ?>'>
<?php if (defined('B2SERRORDEFAULT')) { ?>
    <br>
    <div class="col-md-12 col-xs-12">
        <div class="text-center alert alert-danger"><?php echo B2SERRORDEFAULT; ?></div>
        <br class="clear">
    </div>
<?php } else if (B2SUPATE == 1) { ?>
    <br>
    <div class="col-md-12 col-xs-12">
        <div class="text-center alert alert-danger"><?php echo $lang['B2SUPDATE_INFO']; ?></div>
        <br class="clear">
    </div>
    <?php
} else {
    global $wpdb;
    //Settings
    $wpUserId = get_current_user_id();
    $postsPerPage = 25;
    $currentPage = (int) isset($_GET['b2sPage']) ? $_GET['b2sPage'] : 1;
    $sort = (isset($_GET['sort']) && in_array(trim($_GET['sort']), array('network', 'prg'))) ? trim($_GET['sort']) : '';
    $addSearch = '';
    $addWhere = '';
    $logoutPRG = false;
    $proB2S = false;
    if (isset($_GET['logout']) && (bool) $_GET['logout'] !== false) {
        $logoutPRG = true;
    }
    if (isset($_GET['pro']) && (bool) $_GET['pro'] !== false) {
        $proB2S = true;
    }
    require_once plugin_dir_path(__FILE__) . '../helper/Tools.php';
    require_once plugin_dir_path(__FILE__) . '../helper/Util.php';
    require_once plugin_dir_path(__FILE__) . '../helper/Pagination.php';
    //Info New Feature
    $sqlInfo = $wpdb->prepare("SELECT `feature`  FROM `b2s_user` WHERE `blog_user_id` = %d", $wpUserId);
    $featureInfo = $wpdb->get_row($sqlInfo);
    //CurrentVersion
    $version = json_decode(B2STools::getInfo(B2STOKEN));
    //User
    $addUser = (!current_user_can('unfiltered_html')) ? $wpdb->prepare(' AND `post_author` = %d', $wpUserId) : '';
    //Suche
    if (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) {
        $addSearch = $wpdb->prepare(' AND `post_title` LIKE %s', '%' . trim($_GET['b2sSearch']) . '%');
    }
    //Sortierung
    if (!empty($sort)) {
        $sort = ($sort == 'prg') ? '`last_prg_publish_date` != "0000-00-00 00:00:00"' : '`last_network_publish_date` != "0000-00-00 00:00:00"';
        $addWhere = 'AND `filter`.' . $sort;
    }
    //FirstCall or NO Filter
    $addSelect = (empty($addSearch) && empty($addWhere)) ? '' : ",`filter`.`last_prg_publish_date`,`filter`.`last_network_publish_date`";
    $addLeftJoin = (empty($addSelect)) ? '' : " LEFT JOIN `b2s_filter` AS `filter` ON `$wpdb->posts`.`ID` = `filter`.`post_id`  ";
    $sqlPostsPage = "SELECT `$wpdb->posts`.`ID`, `post_author`, `post_date`, `post_title` $addSelect
		FROM `$wpdb->posts` $addLeftJoin
		WHERE `post_status` = 'publish' AND `post_type` = 'post'  $addUser $addSearch $addWhere
		ORDER BY `ID` DESC
		LIMIT " . (($currentPage - 1) * $postsPerPage) . ",$postsPerPage";
    $posts_array = $wpdb->get_results($sqlPostsPage);
    $sqlNumberPosts = "SELECT COUNT(*)
		FROM `$wpdb->posts` $addLeftJoin
		WHERE `post_status` = 'publish' AND post_type = 'post' $addUser $addSearch $addWhere";
    $numberAllPosts = $wpdb->get_var($sqlNumberPosts);
    ?>
    <noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>
    <input type="hidden" name="blog_user_id" id="blog_user_id" value="<?php echo $wpUserId; ?>">
    <?php if (!is_object($version) || empty($version)) { ?>
        <div class="col-md-12 col-xs-12">
            <br>
            <div class="alert alert-danger">
                <?php echo $lang['NETWORK_DISCONNECT_INFO']; ?>
            </div>
        </div>
    <?php } ?>
    <?php
    //Info New Version
    if (is_object($featureInfo) && isset($featureInfo->feature) && (int) $featureInfo->feature == 0 && !empty($lang['NETWORK_FEATURE_INFO'])) {
        ?>
        <div class="col-md-12 col-xs-12 featureInfo">
            <br>
            <div class="alert alert-success ">
                <button type="button" class="featureInfoClose"><span aria-hidden="true">&times;</span></button>
                <?php echo $lang['NETWORK_FEATURE_INFO']; ?>
            </div>
        </div>
    <?php } ?>
    <div class="col-md-12 col-xs-12">
        <div class="alert alert-danger key-error-1" style="display:none;"><?php echo $lang['NETWORK_KEY_ERROR_1']; ?></div>
        <div class="alert alert-danger key-error-2" style="display:none;"><?php echo $lang['NETWORK_KEY_ERROR_2']; ?></div>
        <div class="alert alert-success key-success" style="display:<?php echo (($proB2S !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['NETWORK_KEY_SUCCESS']; ?></div>
    </div>
    <div style="display:none;" id="b2sLoader">
        <div class="col-md-11 col-xs-11">
            <div class="text-center">
                <img src="<?php echo plugins_url('/assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
                <h3><?php echo $lang['LOADING']; ?></h3>
            </div>
        </div>
    </div>
    <div class="postList">
        <div class="col-md-12 col-xs-12">
            <div class="alert alert-success logoutPRG" style="display:<?php echo (($logoutPRG !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['PRG_LOGOUT_SUCCESS']; ?></div>
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="col-md-8 col-xs-12 posts">
                <div class="page-header">
                    <div class="pull-left page-header-top">
                        <h2><?php echo $lang['MENU_CONTENT']; ?></h2>
                    </div>
                    <div class="pull-right menu-sort">
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <?php echo $lang['POSTS_SORT_TITLE']; ?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#"><b><?php echo $lang['POSTS_SORT_INFO']; ?></b></a></li>
                                <li><a href="?page=blog2social&sort=network<?php echo (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) ? '&b2sSearch=' . $_GET['b2sSearch'] : ''; ?>">- <?php echo $lang['POSTS_SORT_NETWORK']; ?></a></li>
                                <li><a href="?page=blog2social&sort=prg<?php echo (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) ? '&b2sSearch=' . $_GET['b2sSearch'] : ''; ?>">- <?php echo $lang['POSTS_SORT_PRG']; ?></a></li>
                                <li class="divider"></li>
                                <li><a href="?page=blog2social"><b><?php echo $lang['POSTS_SORT_RESET']; ?></b></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="pull-right">
                        <form action="#" method="GET" name="b2sPostSearch" class="b2sPostSearch">
                            <div class="input-group">
                                <input id="page" type="hidden" value="blog2social" name="page">
                                <input id="b2sSearch" class="form-control text-input" type="text" name="b2sSearch" value="<?php echo (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) ? $_GET['b2sSearch'] : ''; ?>" required="true" maxlength="30" placeholder="<?php echo $lang['POSTS_SEARCH_BUTTON_PLACEHOLDER']; ?>">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" type="button">
                                        <?php echo $lang['POSTS_SEARCH_BUTTON']; ?>
                                    </button>
                                </span>
                                <?php if (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) { ?>
                                    <span class="input-group-btn">
                                        <a href="?page=blog2social" class="pull-right btn btn-danger">&times;</a>
                                    </span>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (!empty($posts_array)) { ?>
                    <table class="table">
                        <thead>
                            <tr >
                                <th class="table-header">#</th>
                                <th class="table-header"><?php echo $lang['POSTS_TITLE']; ?></th>
                                <th class="table-header"><?php echo $lang['POSTS_CREATED']; ?></th>
                                <th class="table-header"><?php echo $lang['POSTS_AUTHOR']; ?></th>
                                <th class="table-header"><?php echo $lang['POSTS_PUBLISH']; ?></th>
                                <th class="table-header"></th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($posts_array as $var) {
                            $last_network_publish_date = ($var->last_network_publish_date != '0000-00-00 00:00:00' && !empty($var->last_network_publish_date)) ? date($lang['DATE_FRONTEND'], strtotime($var->last_network_publish_date)) : '';
                            $last_prg_publish_date = ($var->last_prg_publish_date != '0000-00-00 00:00:00' && !empty($var->last_prg_publish_date)) ? date($lang['DATE_FRONTEND'], strtotime($var->last_prg_publish_date)) : '';
                            ?>
                            <tr class="text-center">
                                <td><?php echo $var->ID; ?></td>
                                <td><?php echo $var->post_title; ?></td>
                                <td><?php echo date($lang['DATE_FRONTEND'], strtotime($var->post_date)); ?></td>
                                <td><?php echo the_author_meta('user_nicename', $var->post_author); ?></td>
                                <td width="20%">
                                    <a href="#" id="prg_post_<?php echo $var->ID; ?>" class="btn btn-warning sentPRG" rel='<?php echo json_encode(array('post_id' => $var->ID)); ?>'><?php echo $lang['POSTS_SENT_PRG_BUTTON']; ?></a>
                                    <?php echo (!empty($last_prg_publish_date)) ? '<br><small><i>' . $lang['POSTS_LAST_PUBLISH'] . ' ' . $last_prg_publish_date . '</i></small>' : '<br>' ?>
                                </td>
                                <td width="20%">
                                    <a href="#" class="btn btn-success sentNetwork" rel='<?php echo json_encode(array('post_id' => $var->ID)); ?>'><?php echo $lang['POSTS_SENT_NETWORK_BUTTON']; ?></a>
                                    <?php echo (!empty($last_network_publish_date)) ? '<br><small><i>' . $lang['POSTS_LAST_PUBLISH'] . ' ' . $last_network_publish_date . '</i></small>' : '<br>' ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                <?php } ?>
                <?php if (empty($posts_array)) { ?>
                    <br clear="both">
                    <div class="alert alert-default"><?php echo (isset($_GET['b2sSearch']) || isset($_GET['sort'])) ? $lang['POSTS_SEARCH_EMPTY'] : $lang['POSTS_EMPTY']; ?></div>
                <?php } ?>
                <?php if (!empty($posts_array)) { ?>
                    <br clear="both">
                    <div id="pagination" class="text-center">
                        <?php
                        echo B2SPagination::createPagination($numberAllPosts, $postsPerPage, $currentPage);
                        ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (is_object($version) && !empty($version)) { ?>
                <div class="col-md-4 col-xs-12 pull-right b2s-version version" style="padding-right:0px!important;">
                    <div class="page-header pull-right keyEntry" style="width:280px">
                        <div class="alert alert-info" style="padding: 0px !important;">
                            <img src="<?php echo plugins_url('/assets/images/b2s_stats_user.png', dirname(__FILE__)); ?>">
                            <strong> Version: <?php echo ((int) $version->version == 0) ? $lang['NETWORK_VERSION_FREE_INFO'] : $lang['NETWORK_VERSION_PRO_' . (int) $version->version . '_INFO']; ?></strong>
                            <br>
                            <?php $nextSendDate = (isset($version->nextSentDateTime)) ? date('m/d/Y H:i:s', $version->nextSentDateTime + time()) : '01/01/2015 01:00:00'; ?>
                            <input type="hidden" name="nextSendDate" id="nextSendDate" class="nextSendDate" value="<?php echo $nextSendDate; ?>">
                            <?php if (isset($version->openSent) && isset($version->total)) { ?>
                                <hr>
                                <div class="padding-left"><?php echo $lang['NETWORK_STATS_TITLE']; ?>
                                    <?php $userSentToday = (int) $version->total - $version->openSent; ?>
                                    <br><?php echo $lang['NETWORK_STATS_TODAY']; ?>  <strong><?php echo $userSentToday; ?> von  <?php echo $version->total; ?></strong> <?php echo $lang['NETWORK_STATS_USED']; ?>
                                    <br><br>
                                </div>
                                <ul class="countdown" style="display:<?php echo ($version->openSent == 0) ? 'block' : 'none'; ?>">
                                    <?php echo $lang['NETWORK_STATS_NEXT_SEND_DATE']; ?>  <br><br>
                                    <li> <span class="days">00</span>
                                        <p class="days_ref"><?php echo $lang['NETWORK_STATS_NEXT_SEND_DAY']; ?></p>
                                    </li>
                                    <li class="seperator">.</li>
                                    <li> <span class="hours">00</span>
                                        <p class="hours_ref"><?php echo $lang['NETWORK_STATS_NEXT_SEND_HOUR']; ?></p>
                                    </li>
                                    <li class="seperator">:</li>
                                    <li> <span class="minutes">00</span>
                                        <p class="minutes_ref"><?php echo $lang['NETWORK_STATS_NEXT_SEND_MINUTE']; ?></p>
                                    </li>
                                    <li class="seperator">:</li>
                                    <li> <span class="seconds">00</span>
                                        <p class="seconds_ref"><?php echo $lang['NETWORK_STATS_NEXT_SEND_SECOND']; ?></p>
                                    </li>
                                </ul>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12 pull-right ads" style="padding-right:0px!important;">
                    <div class="page-header pull-right keyEntry" style="width:280px">
                        <form action="#" method="POST" name="b2sKey" class="b2sKey">
                            <div class="input-group padding-no-left">
                                <input id="b2sLizenzKey" width="280px" class="form-control text-input" type="text" name="b2sLizenzKey" value="" required="true"  placeholder="<?php echo $lang['KEY_PLACEHOLDER']; ?>">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" type="button"><?php echo $lang['KEY_BUTTON']; ?></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if ((int) $version->version == 0) { ?>
                    <div class="col-md-4 col-xs-12 pull-right ads" style="padding-right:0px!important;">
                        <a href="http://service.blog2social.com" target="_blank"><img title="B2S Ads" width="280px" class="pull-right" class="img-responsive" src="http://service.blog2social.com/ads/images/b2s_ads_large_2015_<?php echo substr(B2SLANGUAGE, 0, 2); ?>.png"></a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
?>
<br clear="both">
<div class="col-md-12 col-xs-12 b2s-footer">
    <div class="pull-left">
        © <?php echo date('Y'); ?> <a target="_blank" class="btn-link btn" href="http://www.adenion.de" rel="nofollow">Adenion GmbH</a> | <small><?php echo $lang['FOOTER_INFO']; ?></small>
    </div>
    <div class="pull-right">
        <a class="btn-link btn" target="_blank" href="http://service.blog2social.com/<?php echo substr(B2SLANGUAGE, 0, 2); ?>/agb"><?php echo $lang['FOOTER_AGB_BUTTON']; ?></a>
        <a class="btn-link btn" target="_blank" href="http://service.blog2social.com/<?php echo substr(B2SLANGUAGE, 0, 2); ?>/datenschutz"><?php echo $lang['FOOTER_DATENSCHUTZ_BUTTON']; ?></a>
        <a class="btn-link btn" target="_blank" href="http://service.blog2social.com/<?php echo substr(B2SLANGUAGE, 0, 2); ?>/impressum"><?php echo $lang['FOOTER_IMPRESSUM_BUTTON']; ?></a>
    </div>
</div>