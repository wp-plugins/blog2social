<br>
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

<?php if (B2SUPATE == 1) { ?>
    <div class="col-md-12 col-xs-12">    
        <div class="text-center alert alert-danger"><?php echo $lang['B2SUPDATE_INFO']; ?></div>
        <br class="clear">
    </div>
<?php } else if (!isset($_GET['reportId']) || (int) $_GET['reportId'] == 0) { ?>

    <div class="col-md-12 col-xs-12">    
        <div class="text-center alert alert-danger"><?php echo $lang['NETWORK_NO_REPORT']; ?></div>
        <br class="clear">
    </div>
    <?php
} else {
    $postEntry = (int) $_GET['reportId'];
    global $wpdb;
    $wpUserId = get_current_user_id();
    $isAdmin = (current_user_can('administrator')) ? 1 : 0;
    $sqlReport = ($isAdmin == 1) ? "SELECT `post_id`, `last_network_publish_date`, `last_prg_publish_date`, `publishData`
		FROM `b2s_filter` WHERE  `id` = '" . $postEntry . "'" : "SELECT `post_id`, `last_network_publish_date`, `last_prg_publish_date`, `publishData`
		FROM `b2s_filter` WHERE  `id` = '" . $postEntry . "' AND `blog_user_id` = '" . $wpUserId . "'";
    $reportResult = $wpdb->get_results($sqlReport);
    $reportResults = false;
    $last_network_publish_date = "";
    $last_prg_publish_date = "";
    $reportData = "";
    $postTitle = "";

    if (!empty($reportResult) && isset($reportResult[0]->last_network_publish_date)) {
        $postData = get_post($reportResult[0]->post_id);
        if (isset($postData->post_title)) {
            $postTitle = $postData->post_title;
        }
        $last_network_publish_date = ($reportResult[0]->last_network_publish_date != '0000-00-00 00:00:00') ? date($lang['DATE_FRONTEND'], strtotime($reportResult[0]->last_network_publish_date)) : '';
        $last_prg_publish_date = ($reportResult[0]->last_prg_publish_date != '0000-00-00 00:00:00') ? date($lang['DATE_FRONTEND'], strtotime($reportResult[0]->last_prg_publish_date)) : '';
        $reportData = (!empty($reportResult[0]->publishData)) ? $reportResult[0]->publishData : '';
    }
    ?>

    <noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>

    <div class="col-md-12 col-xs-12 report">
        <div class="page-header">
            <div class="pull-left page-header-top">
                <h2><?php echo $lang['REPORT_TITLE']; ?> <small><?php echo (!empty($postTitle)) ? '(' . $lang['REPORT_POST_TITLE'] . ': ' . $postTitle . ')' : ''; ?></small></h2>
                <?php if (!empty($last_network_publish_date)) { ?>
                    <small><i><?php echo $lang['REPORT_LAST_NETWORK_PLUBLISH'] . ' ' . $last_network_publish_date; ?></i></small>
                <?php } ?>
            </div>
            <div class="pull-right">
                <a href="?page=blog2social" class="pull-right btn btn-success"><?php echo $lang['MENU_CONTENT']; ?></a>
            </div>
        </div>

        <?php if (!empty($reportData)) { ?>
            <?php
            $isError = in_array($reportData, array('REPORT_PUBLISH_NO_DATA', 'REPORT_PUBLISH_RESPONSE_EMPTY')) ? $reportData : '';
            if (empty($isError)) {
                ?>
                <table class="table noborder"> 
                    <?php
                    $reportData = unserialize(stripslashes($reportData));
                    $errorType = array('TOKEN', 'CONTENT', 'RIGHT', 'LOGIN');
                    foreach ($reportData as $r => $report) {
                        ?>     
                        <tr class="border-bottom">
                            <td width="5%"><img src="<?php echo plugins_url('assets/images/portale/' . $report['portal_id'] . '_flat.png', dirname(__FILE__)); ?>"></td>
                            <?php $type = (strtolower(substr(B2SLANGUAGE, 0, 2)) != 'de' && strtolower(trim($report['type'])) == 'profil') ? 'profile' : $report['type']; ?>
                            <td width="10%"><div class="report-portal"><?php echo $type . ' | ' . $report['portal_name']; ?></div></td>
                            <td width="20%"><div class="report-info">
                                    <?php $iconName = ((int) $report['error'] == 0) ? 'ok' : 'cancel'; ?>
                                    <img class="image-scale-25" src="<?php echo plugins_url('assets/images/' . $iconName . '.png', dirname(__FILE__)); ?>">
                                    <div class="report-info-text"> 
                                        <?php echo ((int) $report['error'] == 0) ? (empty($report['publishUrl']) ? $lang['REPORT_PUBLISH_TITLE'] : '') : $lang['REPORT_FAILED_TITLE']; ?>
                                        <?php echo (!empty($report['publishUrl'])) ? '<a class="btn btn-success" href="' . $report['publishUrl'] . '" target="_blank">' . $lang['REPORT_PUBLISH_LINK'] . '</a>' : ''; ?>     
                                    </div>
                            </td>
                            <td width="30%"><div class="report-info-fail">
                                    <?php
                                    if ((int) $report['error'] == 1) {
                                        $errorCode = (isset($report['error_code']) && !empty($report['error_code']) && in_array($report['error_code'], $errorType)) ? trim($report['error_code']) : 'DEFAULT';
                                        echo (isset($lang['REPORT_FAILED_' . $errorCode]) && !empty($lang['REPORT_FAILED_' . $errorCode])) ? $lang['REPORT_FAILED_' . $errorCode] : $lang['REPORT_FAILED_DEFAULT'];
                                    }
                                    ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>    
                <?php
            } else {
                ?>
                <br clear="both"> 
                <div class="alert alert-danger"> <?php echo $lang[$isError]; ?></div>
            <?php }
            ?>
        <?php } ?>

        <?php if (empty($reportData)) { ?>
            <br clear="both"> 
            <div class="alert alert-danger"><?php echo $lang['REPORT_NO_DATA']; ?></div>
        <?php } ?>
    </div>
    <?php
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


