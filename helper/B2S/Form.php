<script type="text/javascript" src="<?php echo plugins_url('../assets/lib/modal/js/jquery.leanModal.min.js', dirname(__FILE__)); ?>"></script>
<br>
<div class="col-md-12 col-xs-12">
    <div class="pull-left">
        <a target="_blank" href="http://service.blog2social.com">
            <img class="b2s-logo" src="<?php echo plugins_url('../assets/images/b2s_logo.png', dirname(__FILE__)); ?>">
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
<?php if (B2SUPATE == 1) { ?>
    <br>
    <div class="col-md-12 col-xs-12">
        <div class="text-center alert alert-danger"><?php echo $lang['B2SUPDATE_INFO']; ?></div>
        <br class="clear">
    </div>
    <?php
} else {
    $aktivMandant = 0;
    $infoVersion = '';
    if (!isset($_GET['postId']) || (int) $_GET['postId'] == 0) {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
        exit;
    }
    $blogUserId = get_current_user_id();
    //Beitrag laden
    $postData = get_post((int) $_GET['postId']);
    if (empty($postData) || !isset($postData->ID) || (int) $blogUserId == 0 || ($postData->post_author != $blogUserId && !current_user_can('unfiltered_html'))) {
        $textError = $lang['ERROR_BAD_USER'];
        $showB2SExtension = false;
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
        exit;
    }
    if ($postData->post_status != 'publish') {
        $textError = $lang['ERROR_NO_PUBLISH'];
        $showB2SExtension = false;
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
        exit;
    }
    if (isset($_GET['mandant']) && (int) $_GET['mandant'] > 0) {
        $aktivMandant = (int) $_GET['mandant'];
    }
    //VOE Infos
    global $wpdb;
    $voeData = array();
    $lastVoeDate = '';
    $sqlNetworkData = $wpdb->prepare("SELECT * FROM `b2s_filter` WHERE `blog_user_id` = %d AND `post_id` = %d", $blogUserId, (int) $_GET['postId']);
    $postNetworkData = $wpdb->get_row($sqlNetworkData);
    if (is_object($postNetworkData) && !empty($postNetworkData) && isset($postNetworkData->publishData)) {
        foreach (unserialize($postNetworkData->publishData) as $t => $valueVoe) {
            if (isset($valueVoe['portal_id'])) {
                $voeData[] = $valueVoe['portal_id'];
            }
        }
        $lastVoeDate = $postNetworkData->last_network_publish_date;
    }
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Tools.php';
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Util.php';
//CurrentVersion
    $version = json_decode(B2STools::getInfo(B2STOKEN));
//GetPortale
    $resultPortale = json_decode(B2STools::getPortale(B2STOKEN, 0, $aktivMandant));
//GetMandanten
    $resultMandanten = json_decode(B2STools::getMandanten(B2STOKEN));
    ?>
    <input type="hidden" name="lang" id="lang" value="<?php echo substr(B2SLANGUAGE, 0, 2); ?>">
    <input type='hidden' id='plugin_url' value='<?php echo plugins_url('', dirname(__FILE__)); ?>'>
    <input type='hidden' id="postId" value='<?php echo (int) $_GET['postId']; ?>'>
    <noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>
    <div style="display:none;" id="b2sLoader">
        <div class="col-md-11 col-xs-11">
            <div class="text-center">
                <img src="<?php echo plugins_url('../assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
                <h3><?php echo $lang['LOADING']; ?></h3>
            </div>
        </div>
    </div>
    <?php if (!is_object($resultPortale) || empty($resultPortale)) { ?>
        <div class="col-md-12 col-xs-12">
            <br>
            <div class="alert alert-danger">
                <?php echo $lang['NETWORK_DISCONNECT_INFO']; ?>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($resultPortale) && is_object($resultPortale)) { ?>
        <div class="col-md-7 col-xs-12 leftdiv sentNetwork">
            <?php
            if (isset($version->openSent) && isset($version->nextSentDate) && isset($version->version) && (int) $version->openSent == 0) {
                $nextSendDate = B2SUtil::getCustomDateFormat($version->nextSentDate, substr(B2SLANGUAGE, 0, 2));
                $infoVersion = ((int) $version->version == 0) ? $lang['NETWORK_VERSION_LIMIT_FREE'] . $lang['NETWORK_VERSION_LIMIT_FREE_ADD_1'] . ' ' . $nextSendDate . $lang['NETWORK_VERSION_LIMIT_FREE_ADD_2'] : $lang['NETWORK_VERSION_LIMIT_PRO'] . $lang['NETWORK_VERSION_LIMIT_PRO_ADD_1'] . ' ' . $nextSendDate . $lang['NETWORK_VERSION_LIMIT_PRO_ADD_2'];
                ?>
                <br>
                <div class="alert alert-danger"><?php echo $infoVersion; ?></div>
            <?php } ?>
            <div id="noNetworkSelect" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_NO_SELECT'].$lang['NETWORK_UPDATE_INFO']; ?></div></div>
            <div id="NO_DATA" class="networkError" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_ERROR_NO_DATA']; ?></div></div>
            <div id="RESPONSE_EMPTY" class="networkError" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_ERROR_RESPONSE_EMPTY']; ?></div></div>
            <div id="DATA_EMPTY" class="networkError" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_ERROR_DATA_EMPTY']; ?></div></div>
            <div id="SPAM" class="networkError" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_ERROR_SPAM']; ?></div></div>
            <div id="VERSION" class="networkError" style="display:none;"><br><div class="alert alert-danger"><?php echo $lang['NETWORK_ERROR_VERSION']; ?></div></div>
            <div class="page-header">
                <h2>
                    <?php echo $lang['NETWORK_SHIP_TITLE']; ?> <small>(<?php echo $lang['NETWORK_MANDANT_NAME']; ?>: <span id="mandant-name-select"><?php echo $lang['NETWORK_MANDANT_NAME_DEFAULT']; ?></span>)</small>
                </h2>
            </div>
            <form method="POST" id="b2sNetworkSent" name="b2sNetworkSent" enctype="multipart/form-data">
                <input type='hidden' id="mandant_id_select" name="mandant_id" value='<?php echo $aktivMandant; ?>'>
                <input type='hidden' id='token' name="token" value='<?php echo B2STOKEN; ?>'>
                <input type='hidden' id='token' name="action" value='sentToNetwork'>
                <input type='hidden' id='blog_user_id' name="blog_user_id" value='<?php echo $blogUserId; ?>'>
                <input type='hidden' id='post_id' name="post_id" value='<?php echo (int) $_GET['postId']; ?>'>
                <input type='hidden' id='default_titel' name="default_titel" value='<?php echo strip_tags($postData->post_title); ?>'>
                <?php
                require_once plugin_dir_path(__FILE__) . '../Network.php';
                $imageData = B2SUtil::getImagesByPostID($postData->ID, true);
                $isImage = (is_array($imageData) && !empty($imageData)) ? true : false;
                $postUrl = (get_permalink($postData->ID) !== false) ? get_permalink($postData->ID) : $postData->guid;
                $network = new B2SNetwork($postData, $isImage, $voeData, $lastVoeDate, (int) $version->version, $postUrl, substr(B2SLANGUAGE, 0, 2), $lang, plugins_url('', dirname(__FILE__)));
                foreach ($resultPortale->portale as $p => $portal) {
                    if ((int) $portal->portal_aktiv == 1) {
                        echo $network->getItemHtml($portal);
                    }
                }
                //Image
                ?>
                <input type='hidden' id='default_url' name="default_url" value='<?php echo $postUrl; ?>'>
                <div class="networkItem imageBox">
                    <?php
                    if (!empty($resultPortale) && is_object($resultPortale)) {
                        if ($isImage) {
                            echo'<div class="page-header"><h2>' . $lang['NETWORK_SELECT_IMAGE_TITLE'] . '</h2></div>';
                            $tempCountImage = 0;
                            foreach ($imageData as $key => $image) {
                                $tempCountImage++;
                                echo '<div class="col-sm-6 col-md-4"><div class="thumbnail"><img class="img-thumbnail networkImage" src="' . $image[0] . '" alt="blogImage"><div class="caption text-center"><input ' . (($tempCountImage == 1) ? 'checked="checked"' : '') . ' type="radio" class="checkNetworkImage" name="image_url" value="' . $image[0] . '"></div></div></div>';
                            }
                        } else {
                            ?>
                            <div><?php echo $lang['NETWORK_NO_IMAGE']; ?></div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php if (empty($infoVersion)) { ?>
                    <br clear="both">
                    <button id="btnNetworkSent" style="display:none;" type="submit" class="btn btn-success pull-right"><?php echo $lang['NETWORK_BUTTON_SEND_TITLE']; ?></button>
                <?php } ?>
            </form>
            <?php if (empty($infoVersion)) { ?>
                <div id="b2sNavbarFooter" class="navbar navbar-default navbar-fixed-bottom navbar-small">
                    <button id="btnNetworkNavBar" style="display:none;" class="btn btn-success pull-right-network-sent"><?php echo $lang['NETWORK_BUTTON_SEND_TITLE']; ?></button>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-1 col-xs-12 rightdiv mandant"></div>
        <div class="col-md-4 col-xs-12 rightdiv mandant">
            <div class="page-header">
                <h2>
                    <?php echo $lang['NETWORK_MANDANT_TITLE_SELECT']; ?>
                </h2>
            </div>
            <div class="list-group">
                <?php if (!empty($resultMandanten) && is_object($resultMandanten) && isset($resultMandanten->result) && (int) $resultMandanten->result == 1 && is_array($resultMandanten->mandant) && !empty($resultMandanten->mandant)) { ?>
                    <div class="list-group-item <?php echo ((int) $aktivMandant == 0) ? 'active' : ''; ?>">
                        <h4 class="list-group-item-heading">
                            <div class="pull-left" ><img src="<?php echo plugins_url('../assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
                            <div class="mandant-list-name pull-left"><?php echo $lang['NETWORK_MANDANT_NAME_DEFAULT']; ?></div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-success mandant-button mandantLoad" rel='<?php echo json_encode(array('mandant_id' => 0)); ?>'><?php echo $lang['NETWORK_MANDANT_BUTTON_LOAD']; ?></a>
                            </div>
                        </h4>
                    </div>
                    <?php
                    foreach ($resultMandanten->mandant as $p => $mandant) {
                        $classActive = ((int) $aktivMandant > 0 && (int) $mandant->id == (int) $aktivMandant) ? 'active' : ' ';
                        ?>
                        <div class="list-group-item <?php echo $classActive; ?>">
                            <h4 class="list-group-item-heading">
                                <div class="pull-left"><img src="<?php echo plugins_url('../assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
                                <div id="mandant-name-<?php echo $mandant->id; ?>" class="mandant-list-name pull-left"><?php echo stripcslashes($mandant->name); ?></div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-success mandant-button mandantLoad" rel='<?php echo json_encode(array('mandant_id' => $mandant->id)); ?>' ><?php echo $lang['NETWORK_MANDANT_BUTTON_LOAD']; ?></a>
                                </div>
                            </h4>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="list-group-item active">
                        <h4 class="list-group-item-heading">
                            <div class="pull-left" ><img src="<?php echo plugins_url('../assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
                            <div class="mandant-list-name pull-left"><?php echo $lang['NETWORK_MANDANT_NAME_DEFAULT']; ?></div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-success mandant-button mandantLoad" rel='<?php echo json_encode(array('mandant_id' => 0)); ?>'><?php echo $lang['NETWORK_MANDANT_BUTTON_LOAD']; ?></a>
                            </div>
                        </h4>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <br clear="both">
    <div id="fbPreview" class="modalStyle">
        <a class="modal_close" href="#">&times;</a>
        <div id="fbContainer">
            <div id="fbHead">
                <img class="headObject" id="profilePic" src="<?php echo plugins_url('../assets/images/fb_pic.jpg', dirname(__FILE__)); ?>">
                <div class="headObject" id="userShared">
                    <p><span id="fbUrl"></span> shared a link</p>
                </div>
            </div>
            <div id="fbContent"></div>
            <div id="fbDemo">
                <img id="demoImg" src="">
                <div id="demoContent">
                    <span id="demoTitle"><?php echo $postData->post_title; ?></span><br>
                    <span id="demoUrl"><?php echo $postUrl; ?></span>
                    <div id="demoText"><?php echo substr(trim(strip_shortcodes(strip_tags($postData->post_content))), 0, 120); ?></div>
                </div>
            </div>
        </div>
        <div id="fbFeedbackBar">
            <span id="fbLike">
                Like
            </span>
            <span id="fbComment">
                Comment
            </span>
            <span id="fbPromote">
                Promote
            </span>
            <span id="fbShare">
                Share
            </span>
        </div>
    </div>
<?php } ?>
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