<?php
if (!isset($_GET['blogid'])) {
    exit;
}

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

$published = true;

$page_data = get_page($_GET['blogid']);

if ($page_data->post_status == 'publish' && empty($page_data->post_password)) {

    require_once dirname(__FILE__) . '/../Helper/getWpImages.php';
    require_once dirname(__FILE__) . '/../Helper/getWarrants.php';

    $socials = WarrantsCheck::verifyCreds(substr($userExist->UserBlogToken, 3));

    $facebookName = isset($socials->{"1"}) ? $socials->{"1"} != false ? $socials->{"1"} : '' : '';
    $twitterName = isset($socials->{"2"}) ? $socials->{"2"} != false ? $socials->{"2"} : '' : '';
    $linkedinName = isset($socials->{"3"}) ? $socials->{"3"} != false ? $socials->{"3"} : '' : '';
    $tumblrName = isset($socials->{"4"}) ? $socials->{"4"} != false ? $socials->{"4"} : '' : '';
    $storifyName = isset($socials->{"5"}) ? $socials->{"5"} != false ? $socials->{"5"} : '' : '';
    $pinterestName = isset($socials->{"6"}) ? $socials->{"6"} != false ? $socials->{"6"} : '' : '';
    $flickrName = isset($socials->{"7"}) ? $socials->{"7"} != false ? $socials->{"7"} : '' : '';
} else {
    $published = false;
}

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$userExist->lang];
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
            </style>
            <?php
        elseif (strlen(strstr($agent, "Opera")) > 0) :
            ?>
            <style>
                #preTwitter { padding: 6px 13px !important;}
                #preLinkedin { padding: 6px 13px !important;}
            </style>
            <?php
        elseif (strlen(strstr($agent, "Chrome")) > 0) :
            ?>
            <style> 
                #preTwitter { padding: 8px 13px 6px !important;}
                #preLinkedin { padding: 8px 13px 6px !important;}
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
        <div>
            <form method="POST" id="prgConnect_sendSocial" name="prgConnect_sendSocial" enctype="multipart/form-data">
                <fieldset>
                    <div id="facebookPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($facebookName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkFacebook" type="checkbox" name="checkFacebook" />  Facebook</span>
                                    <img id="statusFacebookLoading" alt="faceLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusFacebookSucc" alt="faceSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusFacebookFail" alt="faceFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <span id="facebookStatusSpan" class="hider">
                                        <?php if (empty($facebookName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                            <?php
                                        else :
                                            echo $text['COMMENT'] . ':';
                                        endif;
                                        ?>
                                    </span>
                                    <a id="btnPreviewFacebook" class="hider pull-right btn btn-xs btn-success"><?php echo $text['PREVIEW_FACEBOOK']; ?></a>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea <?php echo (empty($facebookName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_facebook" name="prg_facebook" class="form-control col-md-10" placeholder="<?php echo $text['COMMENT'] ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="twitterPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($twitterName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkTwitter" type="checkbox" name="checkTwitter" /> Twitter</span>
                                    <img id="statusTwitterLoading" alt="twitLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusTwitterSucc" alt="twitSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusTwitterFail" alt="twitFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <small class="hider"><?php echo $text['NO_PIC_TRANSFER'] . ' | ' . $text['MAX_LENGHT_TWIT']; ?></small>
                                    <span id="twitterStatusSpan" class="hider">
                                        <?php if (empty($twitterName)) : ?>
                                            <small><?php echo $text['SETUP_SN']; ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                    <span id="countdownTwit" class="pull-right hider">

                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <pre id="preTwitter" class="col-md-12"></pre>
                                    <textarea <?php echo (empty($twitterName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_twitter" name="prg_twitter" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_TWITTER']; ?>"><?php echo $page_data->post_title; ?></textarea>
                                    <div class="divOverUrl"></div><br>
                                    <div id="dblClickInfo">
                                        Doubleclick to change the URL
                                    </div>
                                </div>
                                <!--<div class="col-md-12">
                                    <input type="text">
                                </div>-->
                            </div>
                        </div>
                    </div>
                    <div id="linkedinPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($linkedinName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkLinkedin" type="checkbox" name="checkLinkedin" /> LinkedIn</span>
                                    <img id="statusLinkedinLoading" alt="twitLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusLinkedinSucc" alt="twitSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusLinkedinFail" alt="twitFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <small class="hider"><?php echo $text['MAX_LENGHT_LINKEDIN']; ?></small>
                                    <span id="linkedinStatusSpan" class="hider">
                                        <?php if (empty($linkedinName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                    <span id="countdownLink" class="pull-right hider">

                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <pre id="preLinkedin" class="col-md-12"></pre>
                                    <textarea <?php echo (empty($linkedinName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_linkedin" name="prg_linkedin" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_LINKEDIN']; ?>"><?php echo $page_data->post_title; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tumblrPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($tumblrName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkTumblr" type="checkbox" name="checkTumblr"> Tumblr</span>
                                    <img id="statusTumblrLoading" alt="tumblrLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusTumblrSucc" alt="tumblrSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusTumblrFail" alt="tumblrFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <span id="tumblrStatusSpan" class="hider">
                                        <?php if (empty($tumblrName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea <?php echo (empty($tumblrName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_tumblr" name="prg_tumblr" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_TUMBLR']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
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
                                        <div class="col-xs-1" id="divTagAddBtn">
                                            <img id="tagAddBtn" src="<?php echo plugins_url('images/add.png', dirname(__FILE__)); ?>">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="storifyPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($storifyName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkStorify" type="checkbox" name="checkStorify" /> Storify</span>
                                    <img id="statusStorifyLoading" alt="storifyLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusStorifySucc" alt="storifySucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusStorifyFail" alt="storifyFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <small class="hider"><?php echo $text['NO_PIC_TRANSFER']; ?></small>
                                    <span id="storifyStatusSpan" class="hider">
                                        <?php if (empty($storifyName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea <?php echo (empty($storifyName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_storify" name="prg_storify" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_STORIFY']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="pinterestPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($pinterestName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkPinterest" type="checkbox" name="checkPinterest" /> Pinterest</span>
                                    <img id="statusPinterestLoading" alt="pintLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusPinterestSucc" alt="pintSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusPinterestFail" alt="pintFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <small class="hider"><?php echo $text['ONLY_WITH_PIC'] . ' | ' . $text['MAX_LENGHT_PINTEREST']; ?></small>
                                    <span id="pinterestStatusSpan" class="hider">
                                        <?php if (empty($pinterestName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                    <span id="countdownPint" class="pull-right hider">

                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <pre id="prePinterest" class="col-md-12"></pre>
                                    <textarea <?php echo (empty($pinterestName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_pinterest" name="prg_pinterest" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_PINTEREST']; ?>"><?php echo $page_data->post_title; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="flickrPostArea">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <h3>
                                    <span class="label label-primary administrative-label"><input class="snChecks" <?php echo (empty($flickrName)) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkFlickr" type="checkbox" name="checkFlickr" /> Flickr</span>
                                    <img id="statusFlickrLoading" alt="pintLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusFlickrSucc" alt="pintSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <img id="statusFlickrFail" alt="pintFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
                                    <small class="hider"><?php echo $text['ONLY_WITH_PIC']; ?></small>
                                    <span id="flickrStatusSpan" class="hider">
                                        <?php if (empty($flickrName)) : ?>
                                            <small><?php echo $text['SETUP_SN'] ?></small>
                                        <?php endif;
                                        ?>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 hider">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea <?php echo (empty($flickrName)) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_flickr" name="prg_flickr" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_FLICKR']; ?>"><?php echo trim(strip_shortcodes(strip_tags($page_data->post_content))); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="bottomPublishForm" class="hider">
                        <div id="imgChose" class="col-md-12">
                            <div class="col-md-12">
                                <h4>
                                    <span class="label label-primary administrative-label"><?php echo $text['IMG_CHOOSE']; ?></span>
                                    <small><?php echo $text['ALLOWED_IMG_FORMATS'] ?>: .jpg, .png</small>
                                </h4>
                            </div>
                            <div class="col-xs-12">
                                <div id="divImg1" class="col-md-3 picList">
                                    <label class="col-md-12" for="img0"><img class="imgs" alt="placeholder" src="<?php echo plugins_url('/images/placeholder.png', dirname(__FILE__)); ?>"></label>
                                    <div class="text-center">
                                        <input checked="checked" id="img0" type="radio" name="image" value="">
                                    </div>
                                </div>
                                <?php
                                $images = getImagesByPostID($page_data->ID, true);
                                if ($images) :
                                    $i = 0;
                                    foreach ($images as $key => $image) :
                                        $i++;
                                        if ($i == 4 || $i == 8) {
                                            echo '</div><div class="col-xs-12">';
                                        }
                                        ?>
                                        <div class="col-md-3 picList">
                                            <label class="col-md-12" for="img<?php echo $i + 1; ?>"><img alt="wordpressPic" class="imgs" src="<?php echo $image[0]; ?>"></label>
                                            <div class="text-center">
                                                <input class="newImg" id="img<?php echo $i + 1; ?>" type="radio" name="image" value="<?php echo $image[0]; ?>">
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
                <input type="hidden" name="blogid" value="<?php echo $_GET['blogid']; ?>">
                <input type="hidden" name="postTitle" id="postTitle" value="<?php echo $page_data->post_title; ?>">
            </form>
            <input type="hidden" name="facebookName" id="facebookName" value="<?php echo $facebookName; ?>">

        </div>
    </body>
</html>

<?php 

/*
<div id="googleplusPostArea">
	<div class="col-md-12">
		<div class="col-md-12">
			<h3>
				<span class="label label-primary"><input <?php echo ($googleplusRegistered == false) ? 'disabled="disabled"' : 'checked="checked"' ?> id="checkGoogleplus" type="checkbox" name="checkGoogleplus" /> Google+</span>
				<img id="statusGoogleplusLoading" alt="googLoad" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>" style="display:none;width:30px;">
				<img id="statusGoogleplusSucc" alt="googSucc" src="<?php echo plugins_url('images/OK_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
				<img id="statusGoogleplusFail" alt="googFail" src="<?php echo plugins_url('images/Remove_Button.png', dirname(__FILE__)); ?>" style="display:none;width:30px;">
				<span id="googleplusStatusSpan" class="hider">
					<?php if ($googleplusRegistered == false) : ?>
						<small><?php echo $text['SETUP_SN'] ?></small>
					<?php endif;
					?>
				</span>
			</h3>
		</div>
	</div>
	<div class="col-md-12 hider">
		<div class="form-group">
			<div class="col-md-12">
				<textarea <?php echo ($googleplusRegistered == false) ? 'readonly="readonly" style="transparent; color: rgb(172, 168, 153); background-color: rgb(235, 235, 228);"' : ''; ?> id="prg_googleplus" name="prg_googleplus" class="form-control col-md-11" placeholder="<?php echo $text['MSG_TO_GOOGLE']; ?>"><?php echo str_replace('[gallery]', '', trim(strip_tags($page_data->post_content))); ?></textarea>
			</div>
		</div>
	</div>
</div>
 */