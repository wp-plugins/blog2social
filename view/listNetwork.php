<script type="text/javascript" src="<?php echo plugins_url('/assets/lib/modal/js/jquery.leanModal.min.js', dirname(__FILE__)); ?>"></script>
<br>
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
    $aktivMandant = 0;
    $mandantAdd = false;
    $mandantDelete = false;
    if (isset($_GET['add']) && (bool) $_GET['add'] !== false) {
        $mandantAdd = true;
    }
    if (isset($_GET['delete']) && (bool) $_GET['delete'] !== false) {
        $mandantDelete = true;
    }
    if (isset($_GET['mandant']) && (int) $_GET['mandant'] > 0) {
        $aktivMandant = (int) $_GET['mandant'];
    }
    require_once plugin_dir_path(__FILE__) . '../helper/Tools.php';
//GetPortale
    $resultPortale = json_decode(B2STools::getPortale(B2STOKEN, 0, $aktivMandant));
//GetMandanten
    $resultMandanten = json_decode(B2STools::getMandanten(B2STOKEN));
    ?>
    <input type="hidden" name="lang" id="lang" value="<?php echo substr(B2SLANGUAGE, 0, 2); ?>">
    <input type='hidden' id='token' value='<?php echo B2STOKEN; ?>'>
    <input type='hidden' id='plugin_url' value='<?php echo plugins_url('', dirname(__FILE__)); ?>'>
    <input type='hidden' id="mandant_id_select" value='<?php echo $aktivMandant; ?>'>
    <input type="hidden" id="network_connect_name" value='<?php echo $lang['NETWORK_CONNECT']; ?>'>
    <noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>
    <div class="col-md-12 col-xs-12">
        <div class="alert alert-danger mandant-add-error" style="display:none;"><?php echo $lang['NETWORK_MANDANT_ADD_ERROR']; ?></div>
        <div class="alert alert-success mandant-add-success" style="display:<?php echo (($mandantAdd !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['NETWORK_MANDANT_ADD_SUCCESS']; ?></div>
        <div class="alert alert-danger mandant-delete-error" style="display:none;"><?php echo $lang['NETWORK_MANDANT_DELETE_ERROR']; ?></div>
        <div class="alert alert-success mandant-delete-success" style="display:<?php echo (($mandantDelete !== false) ? 'block' : 'none'); ?>;"><?php echo $lang['NETWORK_MANDANT_DELETE_SUCCESS']; ?></div>
        <div class="alert alert-success network-delete-success" style="display:none;"><?php echo $lang['NETWORK_DELETE_SUCCESS']; ?></div>
        <div class="alert alert-danger network-delete-error" style="display:none;"><?php echo $lang['NETWORK_DELETE_ERROR']; ?></div>
    </div>
    <div style="display:none;" id="b2sLoader">
        <div class="col-md-11 col-xs-11">
            <div class="text-center">
                <img src="<?php echo plugins_url('/assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
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
        <div class="col-md-7 col-xs-12 leftdiv network">
            <div id="portale">
                <div class="page-header">
                    <h2>
        <?php echo $lang['NETWORK_TITLE']; ?> <small>(<?php echo $lang['NETWORK_MANDANT_NAME']; ?>: <span id="mandant-name-select"><?php echo $lang['NETWORK_MANDANT_NAME_DEFAULT']; ?></span>)</small>
                    </h2>
                </div>
                <table class="table">
                    <?php
                    foreach ($resultPortale->portale as $p => $portal) {
                        if ((int) $portal->portal_aktiv == 1) {
                            $profil_aktiv = (isset($portal->profil_aktiv) && (int) $portal->profil_aktiv == 1) ? 1 : 0;
                            $connectProfile = ($profil_aktiv == 0) ? array('default', $lang['NETWORK_CONNECT']) : array('success', $lang['NETWORK_CHANGE']);
                            $connectPage = (!isset($portal->page_id) || empty($portal->page_id)) ? array('default', $lang['NETWORK_CONNECT']) : array('success', $lang['NETWORK_CHANGE']);
                            $connectGroup = (!isset($portal->group_id) || empty($portal->group_id)) ? array('default', $lang['NETWORK_CONNECT']) : array('success', $lang['NETWORK_CHANGE']);
                            $accountName = (empty($portal->profil_name) && !isset($portal->profil_name) && (int) $portal->profil_multi == 0) ? '' : $portal->profil_name;
                            $pageId = ((empty($portal->page_id) && !isset($portal->page_id)) || ((int) $portal->page_id == 0)) ? '' : ' ID:' . $portal->page_id;
                            $groupId = ((empty($portal->group_id) && !isset($portal->group_id)) || ((int) $portal->group_id == 0)) ? '' : ' ID:' . $portal->group_id;
                            $accountName = (empty($accountName) && (int) $portal->profil_multi == 1) ? 'Buisness Account*' : $accountName;
                            $pageName = (empty($portal->page_name) && !isset($portal->page_name)) ? '' : $portal->page_name;
                            $groupName = (empty($portal->group_name) && !isset($portal->group_name)) ? '' : $portal->group_name;
                            $mandant_id = (isset($portal->mandant_id) || (int) $aktivMandant > 0) ? ((isset($portal->mandant_id)) ? $portal->mandant_id : $aktivMandant ) : 0;
                            $b2sAuthUrl = 'https://developer.blog2social.com/wp/v1/network/auth.php?b2s_token=' . B2STOKEN . '&portal_id=' . $portal->id . '&transfer=' . $portal->transfer . '&sprache=' . substr(B2SLANGUAGE, 0, 2) . '&mandant_id=' . $mandant_id;
                            ?>
                            <tr>
                                <td><img src="<?php echo plugins_url('assets/images/portale/' . $portal->id . '_flat.png', dirname(__FILE__)); ?>"></td>
                                <td>
                                    <div class="portal-name"><?php echo $portal->name; ?></div>
                                    <div class="account-name account-name-<?php echo $portal->id; ?>" style="display:<?php echo (empty($accountName)) ? 'none' : 'block'; ?>;" >(<?php echo $lang['NETWORK_ACCOUNT_NAME'] . ': ' . $accountName; ?>)</div>
                <?php if (isset($portal->page) && (int) $portal->page == 1) { ?>
                                        <br>
                                        <div class="portal-name"><small><?php echo $lang['NETWORK_PAGE']; ?> <a id class="info-network" name="info-network" href="#info-network" rel="page-info"><img class="network-info" src="<?php echo plugins_url('assets/images/b2s_info_small.png', dirname(__FILE__)); ?>"></a> <span class="page-name page-name-<?php echo $portal->id; ?>" style="display:<?php echo (empty($pageId)) ? 'none' : 'block'; ?>;" >(<?php echo $pageName . $pageId; ?>)</span> </small></div>
                                    <?php } ?>
                <?php if (isset($portal->group) && (int) $portal->group == 1) { ?>
                                        <br>
                                        <div class="portal-name"><small><?php echo $lang['NETWORK_GROUP']; ?>  <a id class="info-network" name="info-network" href="#info-network" rel="group-info"><img class="network-info" rel="group-info" src="<?php echo plugins_url('assets/images/b2s_info_small.png', dirname(__FILE__)); ?>"></a> <span class="group-name group-name-<?php echo $portal->id; ?>" style="display:<?php echo (empty($groupId)) ? 'none' : 'block'; ?>;" >(<?php echo $groupName . $groupId; ?>)</span> </small></div>
                <?php } ?>
                                </td>
                                <td>
                                    <a href="#" id="portal-<?php echo $portal->id; ?>" class="btn btn-<?php echo $connectProfile[0]; ?> userLogin"
                                       onclick="wop('<?php echo $b2sAuthUrl . '&choose=profile'; ?>', 'Blog2Social Network', 'netzwerk',<?php echo $mandant_id; ?>);
                                               return false;
                                       "><?php echo $connectProfile[1]; ?></a>
                                    <?php if ($profil_aktiv == 1) { ?>
                                        <a href="#" id="portal-disconnect-<?php echo $portal->id; ?>" class="btn btn-danger portalDisconnect" rel='<?php echo json_encode(array('mandant_id' => $mandant_id, 'portal_id' => $portal->id)); ?>'><?php echo $lang['NETWORK_DISCONNECT']; ?></a>
                                    <?php } ?>
                <?php if (isset($portal->page) && (int) $portal->page == 1) { ?>
                                        <br><br>
                                        <a href="#" id="portal-<?php echo $portal->id; ?>" class="btn btn-sm btn-<?php echo $connectPage[0]; ?> userLogin"
                                           onclick="wop('<?php echo $b2sAuthUrl . '&choose=page'; ?>', 'Blog2Social Network', 'netzwerk',<?php echo $mandant_id; ?>);
                                                   return false;
                                           "><?php echo $connectPage[1]; ?></a>
                                    <?php } ?>
                <?php if (isset($portal->group) && (int) $portal->group == 1) { ?>
                                        <br><br>
                                        <a href="#" id="portal-<?php echo $portal->id; ?>" class="btn btn-sm btn-<?php echo $connectGroup[0]; ?> userLogin"
                                           onclick="wop('<?php echo $b2sAuthUrl . '&choose=group'; ?>', 'Blog2Social Network', 'netzwerk',<?php echo $mandant_id; ?>);
                                                   return false;
                                           "><?php echo $connectGroup[1]; ?></a>
                <?php } ?>
                                </td>
                                <td></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
                <div class="pull-left">* <?php echo $lang['NETWORK_MULTI_PROFILE_INFO']; ?></div>
            </div>
        </div>
        <div class="col-md-5 col-xs-12 rightdiv mandant">
            <div class="page-header">
                <h2>
        <?php echo $lang['NETWORK_MANDANT_TITLE_ADD']; ?> <a id class="info-network" name="info-network" href="#info-network" rel="profil-info"><img class="profil-info" src="<?php echo plugins_url('assets/images/b2s_info_small.png', dirname(__FILE__)); ?>"></a>
                </h2>
            </div>
            <form action="#" method="POST" name="addMandant" class="addMandant">
                <div class="input-group">
                    <input type="text" name="add_mandant_name" id="add_mandant_name" class="form-control" required="true" maxlength="30" placeholder="<?php echo $lang['NETWORK_MANDANT_NAME_PLACEHOLER']; ?>">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-success" type="button"><?php echo $lang['NETWORK_MANDANT_BUTTON']; ?></button>
                    </span>
                </div>
            </form>
            <div class="page-header">
                <h2>
        <?php echo $lang['NETWORK_MANDANT_TITLE_SELECT']; ?>
                </h2>
            </div>
            <div class="list-group">
        <?php if (!empty($resultMandanten) && is_object($resultMandanten) && isset($resultMandanten->result) && (int) $resultMandanten->result == 1 && is_array($resultMandanten->mandant) && !empty($resultMandanten->mandant)) { ?>
                    <div class="list-group-item <?php echo ((int) $aktivMandant == 0) ? 'active' : ''; ?>">
                        <h4 class="list-group-item-heading">
                            <div class="pull-left" ><img src="<?php echo plugins_url('assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
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
                                <div class="pull-left"><img src="<?php echo plugins_url('assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
                                <div id="mandant-name-<?php echo $mandant->id; ?>" class="mandant-list-name pull-left"><?php echo stripcslashes($mandant->name); ?></div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-success mandant-button mandantLoad" rel='<?php echo json_encode(array('mandant_id' => $mandant->id)); ?>' ><?php echo $lang['NETWORK_MANDANT_BUTTON_LOAD']; ?></a>
                                    <a href="#" class="btn btn-warning mandant-button mandantDelete" rel='<?php echo json_encode(array('mandant_id' => $mandant->id)); ?>'><?php echo $lang['NETWORK_MANDANT_BUTTON_DELETE']; ?></a>
                                </div>
                            </h4>
                        </div>
                    <?php } ?>
        <?php } else { ?>
                    <div class="list-group-item active">
                        <h4 class="list-group-item-heading">
                            <div class="pull-left" ><img src="<?php echo plugins_url('assets/images/b2s_mandant.png', dirname(__FILE__)); ?>" class="img-icon"></div>
                            <div class="mandant-list-name pull-left"><?php echo $lang['NETWORK_MANDANT_NAME_DEFAULT']; ?></div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-success mandant-button mandantLoad" rel='<?php echo json_encode(array('mandant_id' => 0)); ?>'><?php echo $lang['NETWORK_MANDANT_BUTTON_LOAD']; ?></a>
                            </div>
                        </h4>
                    </div>
        <?php } ?>
            </div>
        </div>
    <?php
    }
}
?>
<br clear="both">
<div id="info-network" class="modalStyle">
    <a class="modal_close" href="#">&times;</a>
    <div id="info-text-page"><?php echo $lang['NETWORK_INFO_PAGE']; ?></div>
    <div id="info-text-group"><?php echo $lang['NETWORK_INFO_GROUP']; ?></div>
    <div id="info-text-profil"><?php echo $lang['NETWORK_INFO_PROFIL']; ?></div>
</div>
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