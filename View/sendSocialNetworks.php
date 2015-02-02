<?php
if (!isset($_GET['blogid'])) {
    exit;
}

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

$published = true;

$page_data = get_post($_GET['blogid']);

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$userExist->lang];

if ($page_data->post_status == 'publish' && empty($page_data->post_password)) {

    require_once dirname(__FILE__) . '/../Helper/getWpImages.php';
    require_once dirname(__FILE__) . '/../Helper/getWarrants.php';

    $socials = WarrantsCheck::verifyCreds(substr($userExist->UserBlogToken, 3));

    $hint = $text['CONN_NETWORKS'] . '<br><a href="?page=b2sconfigsocial" target="_parent">' . $text['CLICK_HERE'] . '</a>';

    if (!isset($socials->error) && $socials->error != 'No Portals selected') {
        foreach ($socials as $socialname) {
            if ($socialname == '') {
                continue;
            } else {
                $hint = '';
                break;
            }
        }
    }

    $facebookName = isset($socials->{"1"}) ? $socials->{"1"} != false ? $socials->{"1"} : '' : '';
    $twitterName = isset($socials->{"2"}) ? $socials->{"2"} != false ? $socials->{"2"} : '' : '';
    $linkedinName = isset($socials->{"3"}) ? $socials->{"3"} != false ? $socials->{"3"} : '' : '';
    $tumblrName = isset($socials->{"4"}) ? $socials->{"4"} != false ? $socials->{"4"} : '' : '';
    $storifyName = isset($socials->{"5"}) ? $socials->{"5"} != false ? $socials->{"5"} : '' : '';
    $pinterestName = isset($socials->{"6"}) ? $socials->{"6"} != false ? $socials->{"6"} : '' : '';
    $flickrName = isset($socials->{"7"}) ? $socials->{"7"} != false ? $socials->{"7"} : '' : '';
    $xingName = isset($socials->{"8"}) ? $socials->{"8"} != false ? $socials->{"8"} : '' : '';
    $diigoName = isset($socials->{"9"}) ? $socials->{"9"} != false ? $socials->{"9"} : '' : '';
    $googleplusName = isset($socials->{"10"}) ? $socials->{"10"} != false ? $socials->{"10"} : '' : '';
} else {
    $published = false;
}
$images = getImagesByPostID($page_data->ID, true);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $text['SEND_SN_HEADER']; ?></title>
        <link rel="stylesheet" id="Bootstrap-css"  href="<?php echo plugins_url('css/bootstrap.min.3_0_3.css', dirname(__FILE__)); ?>" type="text/css" media="all" />
        <link rel="stylesheet" id="PRG-SN-Form-css"  href="<?php echo plugins_url('css/b2s_social_form.1_0.css', dirname(__FILE__)); ?>" type="text/css" media="all" />
        <?php wp_print_scripts('jquery'); ?>
        <script type="text/javascript" src="<?php echo plugins_url('js/b2s_social_form.1_0.js', dirname(__FILE__)); ?>"></script>
        <script type="text/javascript" src="<?php echo plugins_url('js/bootstrap.min.3_0_3.js', dirname(__FILE__)); ?>"></script>
        <script type="text/javascript" src="<?php echo plugins_url('js/jquery.validate.min.1_9.js', dirname(__FILE__)); ?>"></script>
        <link rel="stylesheet" id="FancyBoxStyle-css"  href="<?php echo plugins_url('css/jquery.fancybox-1_3_4.css', dirname(__FILE__)); ?>" type="text/css" media="all" />
        <script type="text/javascript" src="<?php echo plugins_url('js/jquery.fancybox-1_3_4.pack.js', dirname(__FILE__)); ?>"></script>
        <?php
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strlen(strstr($agent, "Firefox")) > 0) :
            ?>
            <style>
                #preTwitter { padding: 8px 13px 6px !important;}
                #preLinkedin { padding: 8px 13px 6px !important;}
                #prePinterest { padding: 8px 13px 6px !important;}
                #preXing { padding: 8px 13px 6px !important;}
            </style>
            <?php
        elseif (strlen(strstr($agent, "Opera")) > 0) :
            ?>
            <style>
                #preTwitter { padding: 6px 13px !important;}
                #preLinkedin { padding: 6px 13px !important;}
                #prePinterest { padding: 6px 13px !important;}
                #preXing { padding: 6px 13px !important;}
            </style>
            <?php
        elseif (strlen(strstr($agent, "Chrome")) > 0) :
            ?>
            <style> 
                #preTwitter { padding: 8px 13px 6px !important;}
                #preLinkedin { padding: 8px 13px 6px !important;}
                #prePinterst { padding: 8px 13px 6px !important;}
                #preXing { padding: 8px 13px 6px !important;}
            </style>
            <?php
        endif;
        ?>
    </head>
    <body>
        <input type="hidden" name="lang" id="lang" value="<?php echo $userExist->lang; ?>" />
        <?php
        if (!$published) :
            echo '<input type="hidden" value="1" id="closeFB">
	</body>
</html>';
            exit;
        endif;
        ?>
        <div class="page-header">
            <h2>
                <?php echo $text['TRANSFER_TO_SN']; ?>
            </h2>
        </div>
        <?php
        if ($hint != '') {
            ?>
            <div class="alert alert-danger text-center">
                <?php
                echo '<strong>' . $hint . '</strong>';
                ?>
            </div>
        </body>
    </html>
    <?php
    exit;
}
?>
<div>
    <form method="POST" id="prgConnect_sendSocial" name="prgConnect_sendSocial" enctype="multipart/form-data">
        <fieldset>
            <?php if (!empty($facebookName)) { ?>
                <div id="facebookPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkFacebook" type="checkbox" name="checkFacebook"> Facebook</span></label>
                                <img class="spinner" id="statusFacebookLoading" alt="faceLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusFacebookSucc" alt="faceSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusFacebookFail" alt="faceFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <span id="facebookStatusSpan" class="hider">
                                    <?php
                                    echo $text['COMMENT'] . ':';
                                    ?>
                                </span>
                                <a id="btnPreviewFacebook" class="hider pull-right btn btn-xs btn-success"><?php echo $text['PREVIEW_FACEBOOK']; ?></a>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="prg_facebook" name="prg_facebook" class="form-control col-md-10" placeholder="<?php echo $text['COMMENT'] ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($twitterName)) {
                ?>
                <div id="twitterPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkTwitter" type="checkbox" name="checkTwitter" /> Twitter</span></label>
                                <img class="spinner" id="statusTwitterLoading" alt="twitLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusTwitterSucc" alt="twitSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusTwitterFail" alt="twitFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['NO_PIC_TRANSFER'] . ' | ' . $text['MAX_LENGHT_TWIT']; ?></small>
                                <span id="countdownTwit" class="pull-right hider">

                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <pre id="preTwitter" class="col-md-12"></pre>
                                <textarea id="prg_twitter" name="prg_twitter" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_TWITTER']; ?>"><?php echo $page_data->post_title; ?></textarea>
                                <div class="divOverUrl"></div><br>
                                <div id="dblClickInfo">
                                    <?php
                                    echo $text['DBL_CLICK_TO_CHANGE'];
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($linkedinName)) {
                ?>
                <div id="linkedinPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkLinkedin" type="checkbox" name="checkLinkedin" /> LinkedIn</span></label>
                                <img class="spinner" id="statusLinkedinLoading" alt="twitLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusLinkedinSucc" alt="twitSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusLinkedinFail" alt="twitFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['MAX_LENGHT_LINKEDIN']; ?></small>
                                <span id="countdownLink" class="pull-right hider">

                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <pre id="preLinkedin" class="col-md-12"></pre>
                                <textarea id="prg_linkedin" name="prg_linkedin" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_LINKEDIN']; ?>"><?php echo $page_data->post_title; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($tumblrName)) {
                ?>
                <div id="tumblrPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkTumblr" type="checkbox" name="checkTumblr"> Tumblr</span></label>
                                <img class="spinner" id="statusTumblrLoading" alt="tumblrLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusTumblrSucc" alt="tumblrSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusTumblrFail" alt="tumblrFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="prg_tumblr" name="prg_tumblr" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_TUMBLR']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <h4>
                                            <span class="label label-primary administrative-label">Tags</span>
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-xs-12 tagsTumblr">
                                    <div class="col-xs-3">
                                        <input class="form-control inputTumblrTags" name="prg_tumblr_tags[]">
                                    </div>
                                    <div class="col-xs-1" id="tumblrDivTagAddBtn">
                                        <img id="tumblrTagAddBtn" src="<?php echo plugins_url('images/add.png', dirname(__FILE__)); ?>">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($storifyName)) {
                ?>
                <div id="storifyPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkStorify" type="checkbox" name="checkStorify" /> Storify</span></label>
                                <img class="spinner" id="statusStorifyLoading" alt="storifyLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusStorifySucc" alt="storifySucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusStorifyFail" alt="storifyFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['NO_PIC_TRANSFER']; ?></small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="prg_storify" name="prg_storify" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_STORIFY']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($pinterestName)) {
                ?>
                <div id="pinterestPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkPinterest" type="checkbox" name="checkPinterest" /> Pinterest</span></label>
                                <img class="spinner" id="statusPinterestLoading" alt="pintLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusPinterestSucc" alt="pintSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusPinterestFail" alt="pintFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['ONLY_WITH_PIC'] . ' | ' . $text['MAX_LENGHT_PINTEREST']; ?></small>
                                <span id="countdownPint" class="pull-right hider">

                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <pre id="prePinterest" class="col-md-12"></pre>
                                <textarea id="prg_pinterest" name="prg_pinterest" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_PINTEREST']; ?>"><?php echo $page_data->post_title; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($flickrName)) {
                ?>
                <div id="flickrPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkFlickr" type="checkbox" name="checkFlickr" /> Flickr</span></label>
                                <img class="spinner" id="statusFlickrLoading" alt="pintLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusFlickrSucc" alt="pintSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusFlickrFail" alt="pintFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['ONLY_WITH_PIC']; ?></small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="prg_flickr" name="prg_flickr" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_FLICKR']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($xingName)) {
                ?>
                <div id="xingPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkXing" type="checkbox" name="checkXing"> Xing</span></label>
                                <img class="spinner" id="statusXingLoading" alt="xingLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusXingSucc" alt="xingSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusXingFail" alt="xingFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['NO_PIC_TRANSFER'] . ' | ' . $text['MAX_LENGHT_XING']; ?></small>
                                <span id="countdownXing" class="pull-right hider">

                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <pre id="preXing" class="col-md-12"></pre>
                                <textarea id="prg_xing" name="prg_xing" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_XING']; ?>"><?php echo $page_data->post_title; ?></textarea>
                                <div class="divOverUrlXing"></div><br>
                                <div id="dblClickInfoXing">
                                    <?php
                                    echo $text['DBL_CLICK_TO_CHANGE'];
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($diigoName)) {
                ?>
                <div id="diigoPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkDiigo" type="checkbox" name="checkDiigo"> Diigo</span></label>
                                <img class="spinner" id="statusDiigoLoading" alt="diigoLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusDiigoSucc" alt="diigoSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusDiigoFail" alt="diigoFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <small class="hider"><?php echo $text['NO_PIC_TRANSFER'] . ' | ' . $text['MAX_LENGHT_DIIGO']; ?></small>
                                <span id="countdownDiigo" class="pull-right hider">

                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <pre id="preDiigo" class="col-md-12"></pre>
                                <textarea id="prg_diigo" name="prg_diigo" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_DIIGO']; ?>"><?php echo $page_data->post_title; ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <h4>
                                            <span class="label label-primary administrative-label">Tags</span>
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-xs-12 tagsDiigo">
                                    <div class="col-xs-3">
                                        <input class="form-control inputDiigoTags" name="prg_diigo_tags[]">
                                    </div>
                                    <div class="col-xs-1" id="diigoDivTagAddBtn">
                                        <img id="diigoTagAddBtn" src="<?php echo plugins_url('images/add.png', dirname(__FILE__)); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if (!empty($googleplusName)) {
                ?>
                <div id="googleplusPostArea">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h3>
                                <label><span class="label label-primary administrative-label"><input class="snChecks" checked="checked" id="checkGoogleplus" type="checkbox" name="checkGoogleplus" /> GooglePlus</span></label>
                                <img class="spinner" id="statusGoogleplusLoading" alt="storifyLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusGoogleplusSucc" alt="storifySucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                <img id="statusGoogleplusFail" alt="storifyFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-12 hider">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="prg_googleplus" name="prg_googleplus" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_GOOGLEPLUS']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div id="bottomPublishForm" class="hider">
                <div id="imgChose" class="col-md-12">
                    <div class="col-md-12">
                        <h4>
                            <span><?php echo $text['IMG_CHOOSE']; ?></span>
                            <small><?php echo $text['ALLOWED_IMG_FORMATS']; ?>: .jpg, .png <i>(<?php echo $text['ADD_PIC_TO_ARTICLE']; ?>)</i></small>
                        </h4>
                    </div>
                    <div class="row rowPics">
                        <div id="divImg1" class="col-xs-3 picWithRadio">
                            <div class="col-xs-12 pic">
                                <label class="col-xs-12" for="img0"><img class="imgs" alt="placeholder" src="<?php echo plugins_url('/images/placeholder.png', dirname(__FILE__)); ?>"></label>
                            </div>
                            <div class="text-center select col-xs-12">
                                <input <?php echo ($images) ? '' : 'checked="checked"'; ?> id="img0" type="radio" name="image" value="">
                            </div>
                        </div>
                        <?php
                        if ($images) :
                            $i = 0;
                            foreach ($images as $key => $image) :
                                $i++;
                                if ($i == 4 || $i == 8) {
                                    echo '</div><div class="row rowPics">';
                                }
                                ?>
                                <div class="col-xs-3 picWithRadio">
                                    <div class="col-xs-12 pic">
                                        <label class="col-xs-12" for="img<?php echo $i + 1; ?>"><img alt="wordpressPic" class="imgs" src="<?php echo $image[0]; ?>"></label>
                                    </div>
                                    <div class="text-center select col-xs-12">
                                        <input <?php echo ($i == 1) ? 'checked="checked"' : ''; ?> class="newImg" id="img<?php echo $i + 1; ?>" type="radio" name="image" value="<?php echo $image[0]; ?>">
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <button id="btnSubmitSocial" class="btn btn-primary userbtn" data-color="primary"><?php echo $text['MSG_TO_SN'] ?></button>
        </fieldset>
        <div id="clearbot" class="hider">

        </div>
        <input type="hidden" name="shortUrl" id="shortUrl" value="<?php echo $page_data->guid; ?>">
        <input type="hidden" name="urlForTwit" id="urlForTwit" value="<?php echo $page_data->guid; ?>">
        <input type="hidden" name="urlForXing" id="urlForXing" value="<?php echo $page_data->guid; ?>">
        <input type="hidden" name="blogid" value="<?php echo $_GET['blogid']; ?>">
        <input type="hidden" name="postTitle" id="postTitle" value="<?php echo $page_data->post_title; ?>">
    </form>
    <input type="hidden" name="facebookName" id="facebookName" value="<?php echo $facebookName; ?>">

</div>
</body>
</html>