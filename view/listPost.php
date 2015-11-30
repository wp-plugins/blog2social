<br>
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
        <button type="button" class="pull-right btn btn-danger" onclick="window.open('http://developer.blog2social.com/wp/v2/feedback/?lang=<?php echo substr(B2SLANGUAGE, 0, 2); ?>&token=<?php echo B2STOKEN; ?>', 'Blog2Social Feedback', 'width=650,height=520,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20');">
            <?php echo $lang['FEEDBACK_BUTTON']; ?>
        </button>
    </div>
</div>
<br class="clear">
<br class="clear">

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
    $type = (isset($_GET['type']) && in_array(trim($_GET['type']), array('publish', 'sched'))) ? trim($_GET['type']) : 'all';

    $entryPublishDelete = (isset($_GET['delete']) && (bool) $_GET['delete'] !== false && $type == 'publish') ? true : false;
    $entrySchedDelete = (isset($_GET['delete']) && (bool) $_GET['delete'] !== false && $type == 'sched') ? true : false;
    $entrySchedChange = (isset($_GET['change']) && (bool) $_GET['change'] !== false && $type == 'sched') ? true : false;
    $entrySchedSet = (isset($_GET['sched']) && (bool) $_GET['sched'] !== false && $type == 'sched') ? true : false;

    //Scheduling
    $scheduleMinDate = (substr(B2SLANGUAGE, 0, 2) == 'de') ? date('d-m-Y H:i:00', current_time('timestamp')) : date('Y-m-d h:i:00', current_time('timestamp'));
    $scheduleMaxDate = (substr(B2SLANGUAGE, 0, 2) == 'de') ? date('d-m-Y H:i:00', strtotime("+13 months", current_time('timestamp'))) : date('Y-m-d h:i:00', strtotime("+13 months", current_time('timestamp')));


    $addSearch = '';
    $addWhere = '';
    $addSelect = '';
    $order = 'ID';
    $sortType = "DESC";
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
    $version = json_decode(B2STools::getInfo(B2STOKEN, date('Y-m-d', current_time('timestamp'))));
    //User
    $addUser = (!current_user_can('administrator')) ? $wpdb->prepare(' AND `post_author` = %d', $wpUserId) : '';
    //Suche
    if (isset($_GET['b2sSearch']) && !empty($_GET['b2sSearch'])) {
        $addSearch = $wpdb->prepare(' AND `post_title` LIKE %s', '%' . trim($_GET['b2sSearch']) . '%');
    }
    //Filter: Veröffentlichte Beiträge
    if ($type == 'publish') {
        $schedInfo = json_decode(B2STools::getSchedInfo(B2STOKEN));
        if (is_object($schedInfo) && isset($schedInfo->result) && (int) $schedInfo->result == 1 && isset($schedInfo->data) && !empty($schedInfo->data)) {
            $schedInfoData = unserialize($schedInfo->data);
            foreach ($schedInfoData as $t => $value) {
                $wpdb->update('b2s_filter', array('last_network_publish_date' => $value['publish_date'], 'sched_network_date' => '0000-00-00 00:00:00', 'publishData' => $value['publishData']), array('id' => (int) $value['id']));
            }
        }
        $sort = '`filter`.`sched_network_date` = "0000-00-00 00:00:00"';
        $addSelect = ",`filter`.`id` as `report_id`,`filter`.`last_network_publish_date`,`filter`.`publishData`";
        $addWhere = 'AND ' . $sort;
        $order = "filter`.`last_network_publish_date";
        $sortType = "DESC";
    }
    if ($type == 'sched') {
        $sort = '`filter`.`sched_network_date` != "0000-00-00 00:00:00"';
        $addSelect = ",`filter`.`id` as `sched_id`, `filter`.`sched_network_date`";
        $addWhere = 'AND ' . $sort;
        $order = "filter`.`sched_network_date";
        $sortType = "ASC";
    }
    //AllPost or Filter
    $addLeftJoin = (empty($addSelect)) ? '' : " LEFT JOIN `b2s_filter` AS `filter` ON `$wpdb->posts`.`ID` = `filter`.`post_id`  ";
    $sqlPostsPage = "SELECT `$wpdb->posts`.`ID`, `post_date`, `post_title` $addSelect
		FROM `$wpdb->posts` $addLeftJoin
		WHERE `post_status` = 'publish' AND `post_type` = 'post'  $addUser $addSearch $addWhere
		ORDER BY `" . $order . "` " . $sortType . " 
		LIMIT " . (($currentPage - 1) * $postsPerPage) . ",$postsPerPage";
    $posts_array = $wpdb->get_results($sqlPostsPage);


    $sqlNumberPosts = "SELECT COUNT(*)
		FROM `$wpdb->posts` $addLeftJoin
		WHERE `post_status` = 'publish' AND post_type = 'post' $addUser $addSearch $addWhere";
    $numberAllPosts = $wpdb->get_var($sqlNumberPosts);

    //NEXTSCHED
    $sqlNextSchedPost = "SELECT sched_network_date FROM b2s_filter WHERE blog_user_id = " . $wpUserId . " AND sched_network_date != '0000-00-00 00:00:00' ORDER BY sched_network_date ASC LIMIT 1";
    $nextSchedDate = $wpdb->get_var($sqlNextSchedPost);
    ?>

    <input type="hidden" name="isAdmim" id="isAdmin" value="<?php echo ((current_user_can('administrator')) ? 1 : 0 ); ?>">
    <input type="hidden" name="lang" id="lang" value="<?php echo substr(B2SLANGUAGE, 0, 2); ?>">
    <input type='hidden' id='token' value='<?php echo B2STOKEN; ?>'>
    <input type='hidden' id='prg_token' value='<?php echo (isset($_SESSION['b2s_prg_token']) && !empty($_SESSION['b2s_prg_token'])) ? $_SESSION['b2s_prg_token'] : 0; ?>'>
    <input type='hidden' id='prg_id' value='<?php echo (isset($_SESSION['b2s_prg_id']) && !empty($_SESSION['b2s_prg_id'])) ? $_SESSION['b2s_prg_id'] : 0; ?>'>
    <input type='hidden' id='plugin_url' value='<?php echo plugins_url('', dirname(__FILE__)); ?>'>

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
    <div class="col-md-12 col-xs-12 col-sm-12">
        <div id="POSTS_DELETE_SUCCESS" class="alert alert-success" style="display:<?php echo (($entryPublishDelete !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['POSTS_DELETE_SUCCESS']; ?></div>
        <div id="POSTS_DELETE_NO_DATA" class="alert alert-danger" style="display:none;"><?php echo $lang['POSTS_DELETE_NO_DATA']; ?></div>
        <div id="POSTS_DELETE_NO_OWNER" class="alert alert-danger" style="display:none;"><?php echo $lang['POSTS_DELETE_NO_OWNER']; ?></div>
        <div id="SCHED_DELETE_SUCCESS" class="alert alert-success" style="display:<?php echo (($entrySchedDelete !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['SCHED_DELETE_SUCCESS']; ?></div>
        <div id="SCHED_DELETE_NO_DATA" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_DELETE_NO_DATA']; ?></div>
        <div id="SCHED_DELETE_NO_OWNER" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_DELETE_NO_OWNER']; ?></div>
        <div id="SCHED_DELETE_IS_LOCK" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_DELETE_IS_LOCK']; ?></div>
        <div id="SCHED_CHANGE_SUCCESS" class="alert alert-success" style="display:<?php echo (($entrySchedChange !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['SCHED_CHANGE_SUCCESS']; ?></div>
        <div id="SCHED_SET_SUCCESS" class="alert alert-success" style="display:<?php echo (($entrySchedSet !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['SCHED_SET_SUCCESS']; ?></div>
        <div id="SCHED_CHANGE_NO_DATA" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_CHANGE_NO_DATA']; ?></div>
        <div id="SCHED_CHANGE_NO_OWNER" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_CHANGE_NO_OWNER']; ?></div>
        <div id="SCHED_CHANGE_IS_LOCK" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_CHANGE_IS_LOCK']; ?></div>
        <div id="SCHED_CHANGE_LIMIT" class="alert alert-danger" style="display:none;"><?php echo $lang['SCHED_CHANGE_LIMIT']; ?></div>      
        <div id="SHIP_NO_DATA" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_NO_DATA']; ?></div>
        <div id="SHIP_RESPONSE_EMPTY" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_RESPONSE_EMPTY']; ?></div>
        <div id="SHIP_DATA_EMPTY" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_DATA_EMPTY']; ?></div>
        <div id="SHIP_SPAM" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_SPAM']; ?></div>
        <div id="SHIP_VERSION" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_VERSION']; ?></div>    
        <div id="SHIP_LIMIT" class="alert alert-danger" style="display:none;"><?php echo $lang['NETWORK_ERROR_LIMIT']; ?></div>
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
        <div class="col-md-12 col-xs-12 col-sm-12">
            <div class="alert alert-success logoutPRG" style="display:<?php echo (($logoutPRG !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['PRG_LOGOUT_SUCCESS']; ?></div>
        </div>
        <div class="col-md-12 col-xs-12 col-sm-12">
            <div class="col-md-8 col-xs-12 col-sm-12 posts">
                <div class="page-header">
                    <div class="btn-group">
                        <a class="btn btn-<?php echo ($type == 'all' ? 'success' : 'link'); ?>" href="?page=blog2social&type=all">  <?php echo $lang['MENU_CONTENT_ALL']; ?>*</a>
                        <a class="btn btn-<?php echo ($type == 'publish' ? 'success' : 'link'); ?> menu-sort-left" href="?page=blog2social&type=publish"> <?php echo $lang['MENU_CONTENT_PUBLISH']; ?></a>
                        <a class="btn btn-<?php echo ($type == 'sched' ? 'success' : 'link'); ?> menu-sort-left" href="?page=blog2social&type=sched"> <?php echo $lang['MENU_CONTENT_SCHED']; ?></a>
                    </div>
                    <div class="pull-right hidden-search" style="min-width: 150px;">
                        <form action="#" method="GET" name="b2sPostSearch" class="b2sPostSearch">
                            <div class="input-group">
                                <input id="page" type="hidden" value="blog2social" name="page">
                                <input id="type" type="hidden" value="<?php echo $type; ?>" name="type">
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
                <!--VIEW: ALL POSTS -->
                <?php if (!empty($posts_array) && $type == 'all') { ?>
                    <table class="table">
                        <thead>
                            <tr >
                                <td class="table-header"><?php echo $lang['POSTS_TITLE']; ?></td>
                                <td class="table-header"><?php echo $lang['POSTS_ALL_ACTIONS']; ?></td>
                                <td class="table-header"></td>
                            </tr>
                        </thead>
                        <?php
                        foreach ($posts_array as $var) {
                            ?>
                            <tr class="text-right">
                                <td width="70%"><a target="_blank" href="<?php echo get_permalink($var->ID) ?>"><?php echo (strlen(trim($var->post_title)) > 105 ? substr($var->post_title, 0, 102) . '...' : $var->post_title); ?></a></td>
                                <td width="15%" class="text-center">
                                    <a href="#" id="prg_post_<?php echo $var->ID; ?>" class="btn btn-warning sentPRG" rel='<?php echo json_encode(array('post_id' => $var->ID)); ?>'><?php echo $lang['POSTS_SENT_PRG_BUTTON']; ?></a>
                                </td>
                                <td width="15%" class="text-center">
                                    <a href="#" class="btn btn-success sentNetwork" rel='<?php echo json_encode(array('post_id' => $var->ID)); ?>'><?php echo $lang['POSTS_SENT_NETWORK_BUTTON']; ?></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <!-- VIEW: PUBLISH POSTS -->
                <?php } else if (!empty($posts_array) && $type == 'publish') { ?>
                    <table class="table">
                        <thead>
                            <tr >
                                <td class="table-header"><?php echo $lang['POSTS_TITLE']; ?></td>
                                <td class="table-header"><?php echo $lang['POSTS_PUBLISH_ACTIONS']; ?></td>
                                <td class="table-header"></td>
                            </tr>
                        </thead>
                        <?php
                        foreach ($posts_array as $var) {
                            ?>
                            <tr class="text-right">
                                <td width="70%">
                                    <a target="_blank" href="<?php echo get_permalink($var->ID) ?>"><?php echo (strlen(trim($var->post_title)) > 105 ? substr($var->post_title, 0, 102) . '...' : $var->post_title); ?></a>
                                    <br>
                                    <a class="entry-network entry-icon" rel='<?php echo json_encode(array('type' => 'publishDeleteNetwork', 'network_id' => $var->report_id)); ?>' href="#entry-network" name="entry-network"><?php echo $lang['POSTS_DELETE_ACTION_BUTTON']; ?></a> 
                                    <a href="#" class="sentNetwork entry-icon" rel='<?php echo json_encode(array('post_id' => $var->ID)); ?>'>&middot; <?php echo $lang['POSTS_PUBLISH_AGAIN_ACTION_BUTTON']; ?></a>
                                </td>
                                <td width="15%">
                                    <?php
                                    $last_network_publish_date = (isset($var->last_network_publish_date) && $var->last_network_publish_date != '0000-00-00 00:00:00' && !empty($var->last_network_publish_date)) ? B2SUtil::getCustomDateFormat($var->last_network_publish_date, substr(B2SLANGUAGE, 0, 2)) : '';
                                    if (!empty($last_network_publish_date)) {
                                        $error = in_array($var->publishData, array('REPORT_PUBLISH_NO_DATA', 'REPORT_PUBLISH_RESPONSE_EMPTY')) ? '<img class="warning-image" src="' . plugins_url('/assets/images/warning.png', dirname(__FILE__)) . '">' : '';
                                        ?>
                                        <span class="time-padding"><?php echo $error . ' ' . $last_network_publish_date; ?></span><br>
                                    <?php } ?>
                                </td>
                                <td width="15%" class="text-center">
                                    <?php if (!empty($last_network_publish_date)) { ?>
                                        <a href="?page=reportB2S&reportId=<?php echo (int) trim($var->report_id); ?>" class="btn btn-success"> <?php echo $lang['POSTS_REPORT_SHOW_ACTION_BUTTON']; ?></a> 
                                        <br>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <!-- VIEW: SCHED POSTS -->
                <?php } else if (!empty($posts_array) && $type == 'sched') { ?>
                    <table class="table">
                        <thead>
                            <tr >
                                <td class="table-header"><?php echo $lang['POSTS_TITLE']; ?></td>
                                <td class="table-header"><?php echo $lang['POSTS_SCHED_ACTIONS']; ?></td>
                                <td class="table-header"></td>
                            </tr>
                        </thead>
                        <?php
                        foreach ($posts_array as $var) {
                            ?>
                            <tr class="text-right">
                                <td width="70%">
                                    <a target="_blank" href="<?php echo get_permalink($var->ID) ?>"><?php echo (strlen(trim($var->post_title)) > 105 ? substr($var->post_title, 0, 102) . '...' : $var->post_title); ?></a>
                                    <br>
                                    <a class="entry-network entry-icon" rel='<?php echo json_encode(array('type' => 'schedDeleteNetwork', 'network_id' => $var->sched_id)); ?>' href="#entry-network" name="entry-network"><?php echo $lang['POSTS_DELETE_ACTION_BUTTON']; ?></a>                 
                                    <div class="b2sSchedDatePicker" rel='<?php echo B2SUtil::getCustomDate($var->sched_network_date, 'm d Y H:i:s'); ?>'></div>
                                    <input type="text" value="" class="b2sInputChangeSched" rel='<?php echo json_encode(array('sched_id' => $var->sched_id)); ?>' style="display:none;" name="user_sched_date-<?php echo $var->sched_id; ?>" class="form-control" data-field="datetime" data-min="<?php echo $scheduleMinDate; ?>" data-max="<?php echo $scheduleMaxDate; ?>">
                                    <a href="#" class="schedNewNetwork entry-icon">&middot; <?php echo $lang['POSTS_SCHED_NEW_ACTION_BUTTON']; ?></a>  
                                    <a href="#" class="schedPublishNetwork entry-icon" rel='<?php echo json_encode(array('sched_id' => $var->sched_id)); ?>'>&middot; <?php echo $lang['POSTS_PUBLISH_NOW_ACTION_BUTTON']; ?></a>
                                </td>
                                <td width="15%">
                                    <span class="time-padding"><?php echo B2SUtil::getTimeRemainingForToday($var->sched_network_date, date('Y-m-d H:i:s', current_time('timestamp')), substr(B2SLANGUAGE, 0, 2)); ?></span>
                                </td>
                                <td width="15%">
                                    <a class="showSchedNetwork btn btn-success" name="showSchedNetwork" href="#showSchedNetwork" rel='<?php echo json_encode(array('sched_id' => $var->sched_id)); ?>'><?php echo $lang['POSTS_SCHED_SHOW_NETWORK_BUTTON']; ?></a>   
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table> 
                <?php } ?>

                <!--VIEW:DEFAULT ISEMPTY -->
                <?php if (empty($posts_array)) { ?>
                    <br clear="both">
                    <div class="alert alert-default"><?php echo (isset($_GET['b2sSearch'])) ? $lang['POSTS_SEARCH_EMPTY'] : $lang['POSTS_EMPTY_' . strtoupper(trim($type))]; ?></div>
                    <br clear="both">
                <?php } ?>
                <small>* <?php echo $lang['POSTS_VIEW_INFO']; ?></small>


                <!--PAGEINATION: ALL POSTS -->
                <?php if (!empty($posts_array) && $type == 'all') { ?>
                    <br clear="both">
                    <div id="pagination" class="text-center">
                        <?php
                        echo B2SPagination::createPagination($numberAllPosts, $postsPerPage, $currentPage);
                        ?>
                    </div>
                <?php } ?>
            </div>

            <?php if (is_object($version) && !empty($version)) { ?>
                <div class="col-md-4 col-xs-12 col-sm-12 hidden-xs">
                    <div class="col-md-12 col-xs-12 col-sm-12 b2s-version version pull-right" style="padding-right:0px!important;">
                        <div class="page-header pull-right keyEntry" style="min-width:280px">
                            <div class="alert alert-info" style="padding: 0px !important;">
                                <img src="<?php echo plugins_url('/assets/images/b2s_stats_user.png', dirname(__FILE__)); ?>">
                                <strong> Version: <?php echo ((int) $version->version == 0) ? $lang['NETWORK_VERSION_FREE_INFO'] : $lang['NETWORK_VERSION_PRO_' . (int) $version->version . '_INFO']; ?></strong>
                                <br>
                                <?php $nextSendDate = (isset($nextSchedDate) && $nextSchedDate != NULL && $nextSchedDate != '0000-00-00 00:00:00') ? date('m/d/Y H:i:s', strtotime($nextSchedDate)) : '01/01/2015 01:00:00'; ?>
                                <input type="hidden" name="nextSendDate" id="nextSendDate" class="nextSendDate" value="<?php echo $nextSendDate; ?>">

                                <?php if (isset($version->totalPublish) && isset($version->totalSched)) { ?>
                                    <hr>
                                    <div class="padding-left padding-bottom">
                                        <?php echo $lang['NETWORK_STATS_PUBLISH']; ?> <small>(<?php echo strtolower($lang['NETWORK_STATS_TODAY']); ?>)</small>: <?php echo (int) $version->totalPublish; ?> 
                                        <br>
                                        <?php echo $lang['NETWORK_STATS_SCHED']; ?> <small>(<?php echo strtolower($lang['NETWORK_STATS_TODAY']); ?>)</small>:  <?php echo (int) $version->totalSched; ?> 
                                        <br>
                                    </div>
                                    <ul class="countdown" style="display:<?php echo (isset($nextSchedDate) && $nextSchedDate != NULL && $nextSchedDate != '0000-00-00 00:00:00') ? 'block' : 'none'; ?>">
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
                    <div class="col-md-12 col-xs-12 col-sm-12 ads pull-right" style="padding-right:0px!important;">
                        <div class="page-header pull-right keyEntry" style="min-width:280px">
                            <form action="#" method="POST" name="b2sKey" class="b2sKey">
                                <div class="input-group padding-no-left">
                                    <input id="b2sLizenzKey" width="280px" class="form-control text-input" type="text" name="b2sLizenzKey" value="" required="true"  placeholder="<?php echo $lang['KEY_PLACEHOLDER']; ?>">
                                    <span class="input-group-btn">
                                        <?php $buttonKeyText = (is_object($version) && !empty($version) && isset($version->version) && (int) $version->version > 0) ? $lang['KEY_BUTTON_CHANGE'] : $lang['KEY_BUTTON']; ?>
                                        <button type="submit" class="btn btn-success" type="button"><?php echo $buttonKeyText; ?></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if ((int) $version->version == 0) { ?>
                        <div class="col-md-12 col-xs-12 col-sm-12 ads pull-right" style="padding-right:0px!important;">
                            <a href="http://service.blog2social.com" target="_blank"><img title="B2S Ads" width="280px" class="pull-right" class="img-responsive" src="http://service.blog2social.com/ads/images/b2s_ads_large_2015_<?php echo substr(B2SLANGUAGE, 0, 2); ?>.png"></a>
                        </div>
                    <?php } ?>
                </div>
            </div>        
        </div>
        <?php
    }
}
?>

<div id="entry-network" class="modalStyle"> 
    <div class="modal-publishDeleteNetwork">
        <h2><?php echo $lang['NETWORK_POST_PUBLISH_DELETE_TITLE'] ?></h2>
        <p><?php echo $lang['NETWORK_POST_PUBLISH_DELETE_DESC'] ?></p><br/>
        <a class="modal_close btn btn-default" href="#"><?php echo $lang['NETWORK_POST_PUBLISH_DELETE_BUTTON_CANCEL'] ?></a> 
        <a href="#" class=" btn btn-success publishDeleteNetwork" rel="0"><?php echo $lang['NETWORK_POST_PUBLISH_DELETE_BUTTON_DELETE'] ?></a>
    </div>
    <div class="modal-schedDeleteNetwork">
        <h2><?php echo $lang['NETWORK_POST_SCHED_DELETE_TITLE'] ?></h2>
        <p><?php echo $lang['NETWORK_POST_SCHED_DELETE_DESC'] ?></p><br/>
        <a class="modal_close btn btn-default" href="#"><?php echo $lang['NETWORK_POST_SCHED_DELETE_BUTTON_CANCEL'] ?></a> 
        <a href="#" class="btn btn-success schedDeleteNetwork" rel="0"><?php echo $lang['NETWORK_POST_SCHED_DELETE_BUTTON_DELETE'] ?></a> 
    </div>
</div>    

<div id="showSchedNetwork" class="modalStyle"> 
    <a class="modal_close modal_close_button" href="#">&times;</a>
    <h2><?php echo $lang['NETWORK_POST_SHOW_SCHED_LIST_TITLE'] ?></h2>
    <div style="display:none;" id="b2sLoaderModal">
        <div class="col-md-11 col-xs-11">
            <div class="text-center">
                <img src="<?php echo plugins_url('/assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
                <h3><?php echo $lang['LOADING']; ?></h3>
            </div>
        </div>
    </div>
    <div id="showSchedList"></div>
</div>  



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