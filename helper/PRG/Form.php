<?php
//session_start();

if (!isset($_GET['postId']) || (int) $_GET['postId'] == 0 || !isset($_SESSION['b2s_prg_id']) || !isset($_SESSION['b2s_prg_token'])) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
    exit;
}


$userLang = (!isset($_GET['lang']) || !in_array(trim(strip_tags($_GET['lang'])), array('de', 'en'))) ? 'en' : trim(strip_tags($_GET['lang']));

$postId = (int) $_GET['postId'];
$blogUserId = get_current_user_id();

//Beitrag laden
$postData = get_post($postId);


if (empty($postData) || !isset($postData->ID) || (int) $blogUserId == 0) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
    exit;
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Tools.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Util.php';

$category = B2STools::getPRGCategory();
$catXml = simplexml_load_string($category);
$country = B2STools::getPRGCountry();
$countyXml = simplexml_load_string($country);

if (empty($catXml) || empty($countyXml)) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
    exit;
}

global $wpdb;

$sqlUserData = $wpdb->prepare("SELECT * FROM `b2s_user_contact` WHERE `blog_user_id` = %d", $blogUserId);
$userData = $wpdb->get_row($sqlUserData);

$prgKeyName = 'titel_' . $userLang;

$cats = '';
foreach ($catXml as $val) {
    $cats .= '<option value="' . $val->id . '">' . $val->$prgKeyName . '</option>' . PHP_EOL;
}

$countries = '';
foreach ($countyXml as $val) {
    $countries .= '<option value="' . $val->tag . '"';
    if (!empty($userData) && isset($userData->land)) {
        if ($val->tag == $userData->land) {
            $countries .= ' selected="selected"';
        }
    }
    $countries .= '>' . $val->$prgKeyName . '</option>' . PHP_EOL;
}

$images = B2SUtil::getImagesByPostId($postData->ID, false);

$message = strip_shortcodes(strip_tags(trim($postData->post_content)), '<a>');
$title = strip_shortcodes(strip_tags(trim($postData->post_title)), '<a>');
?>

<input type="hidden" value="<?php echo $blogUserId; ?>" id="blog_user_id" name="blog_user_id">
<input type="hidden" value="<?php echo $postId; ?>" id="post_id" name="post_id">

<link rel = "stylesheet" id = "B2SEXTBOOTCSS" href = "<?php echo plugins_url('../assets/css/bootstrap.min.css', dirname(__FILE__)); ?>" type = "text/css" media = "all">

<div class="col-md-12 prg-header">
    <div class="pull-left">
        <a target="_blank" href="http://www.pr-gateway.de">
            <img class="prg-logo" src="<?php echo plugins_url('../assets/images/prg_logo.png', dirname(__FILE__)); ?>">
        </a>
    </div>
    <div class="pull-right prg-account">
        <img class="prg-account-logo" src="<?php echo plugins_url('../assets/images/b2s_mandant.png', dirname(__FILE__)); ?>">
        <span style="margin-top: 5px;">    
            Account: <strong><?php echo $_SESSION['b2s_prg_id']; ?></strong>
            <a href="#" id="logoutPRG" class="btn btn-warning btn-sm"><?php echo $lang['PRG_LOGOUT_BUTTON']; ?></a>
        </span>
    </div>
</div>

<br clear="both">

<noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>

<div style="display:none;" id="b2sLoader">
    <div class="col-md-12 col-xs-12"> 
        <br>
        <div class="text-center">
            <img src="<?php echo plugins_url('../assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
            <h3><?php echo $lang['LOADING']; ?></h3>
        </div>
    </div>
</div>
<div style="display:none;" class="sentMessagePRGSuccess">
    <div class="col-md-12 col-xs-12"> 
        <br>   
        <div class="text-center">
            <div class="alert alert-success"><?php echo $lang['PRG_SENT_MESSAGE_SUCCESS']; ?></div>
        </div>
    </div>
</div>

<div style="display:none;" class="sentMessagePRGWarning">
    <div class="col-md-12 col-xs-12"> 
        <br>
        <div class="text-center">
            <div class="alert alert-warning"><?php echo $lang['PRG_SENT_MESSAGE_WARNING']; ?></div>
        </div>
    </div>
</div>

<div class="col-md-12">  
    <div class="sentMessagePRG">
        <form method="POST" id="sentMessagePRG" enctype="multipart/form-data">
            <fieldset>
                <div id="leftPublishForm">
                    <h3>
                        <span class="label label-primary">1</span>
                        <?php echo $lang['PRG_TEXT']; ?>
                    </h3>

                    <div class="form-group">
                        <label class="col-md-6"><small><?php echo $lang['PRG_CATEGORY']; ?></small></label>
                        <label class="col-md-5"><small><?php echo $lang['PRG_SPEAK']; ?></small></label>
                        <div class="col-md-6">
                            <select name="kategorie_id" id="prg_cat" class="form-control">
                                <?php echo $cats; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select name="sprache" id="sprache" class="form-control">
                                <option value="de"><?php echo $lang['PRG_GERMAN']; ?></option>
                                <option value="en"><?php echo $lang['PRG_ENGLISH']; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_TITLE']; ?></small></label>
                        <div class="col-md-11">
                            <input id="prg_title" name="title" maxlength="150" placeholder="<?php echo $lang['PRG_TITLE_PLACEHOLDER']; ?>" class="form-control" type="text" value="<?php echo $title; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_SUBLINE']; ?> </small></label>
                        <div class="col-md-11">
                            <input id="prg_subline" name="subline" placeholder="<?php echo $lang['PRG_SUBLINE_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_VIDEO']; ?>  </small></label>
                        <div class="col-md-11">
                            <input id="prg_videolink" name="video_link" placeholder="<?php echo $lang['PRG_VIDEO_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_MESSAGE']; ?></small></label>
                        <div class="col-md-11">                     
                            <textarea id="prg_message" name="message" rows="10" data-provide="markdown" class="form-control" placeholder="<?php echo $lang['PRG_MESSAGE_PLACEHOLDER']; ?>"><?php echo $message; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_KEYWORD']; ?> </small></label>
                        <div class="col-md-11">                     
                            <input id="prg_keywords" name="keywords" maxlength="200" placeholder="<?php echo $lang['PRG_KEYWORD_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $lang['PRG_SHORTTEXT']; ?> </small></label>
                        <div class="col-md-11">                     
                            <textarea id="prg_shorttext" name="shorttext" rows="4" class="form-control" placeholder="<?php echo $lang['PRG_SHORTTEXT_PLACEHOLDER']; ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div id="rightPublishForm">
                    <div class="col-md-12">
                        <ul id="formContact" class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#mandant"><?php echo $lang['PRG_MANDANT_TITLE']; ?></a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#presse"><?php echo $lang['PRG_PRESSE_TITLE']; ?></a>
                            </li>
                        </ul>
                    </div>
                    <div id="myTabContent" class="tab-content">
                        <div id="mandant" class="tab-pane active">
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_NAME']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_name_mandant" name="name_mandant" placeholder="<?php echo $lang['PRG_MANDANT_NAME_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4"></label>
                                <label class="col-md-4"><small><?php echo $lang['PRG_MANDANT_VORNAME']; ?></small></label>
                                <label class="col-md-4"><small><?php echo $lang['PRG_MANDANT_NACHNAME']; ?></small></label>
                                <div class="col-md-4">
                                    <select name="anrede_mandant" id="prg_anrede_mandant" class="form-control">
                                        <option value="0"><?php echo $lang['PRG_MRS']; ?></option>
                                        <option value="1"><?php echo $lang['PRG_MR']; ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_vorname_mandant" name="vorname_mandant" placeholder="<?php echo $lang['PRG_MANDANT_VORNAME_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_nachname_mandant" name="nachname_mandant" placeholder="<?php echo $lang['PRG_MANDANT_NACHNAME_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-9"><small><?php echo $lang['PRG_MANDANT_STRASSE']; ?></small></label>
                                <label class="col-md-3"><small><?php echo $lang['PRG_MANDANT_NUMMER']; ?></small></label>
                                <div class="col-md-9">
                                    <input id="prg_strasse_mandant" name="strasse_mandant" placeholder="<?php echo $lang['PRG_MANDANT_STRASSE_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-3">
                                    <input id="prg_nummer_mandant" maxlength="10" name="nummer_mandant" placeholder="<?php echo $lang['PRG_MANDANT_NUMMER_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3"><small><?php echo $lang['PRG_MANDANT_PLZ']; ?></small></label>
                                <label class="col-md-9"><small><?php echo $lang['PRG_MANDANT_ORT']; ?></small></label>
                                <div class="col-md-3">
                                    <input id="prg_plz_mandant" name="plz_mandant" maxlength="10" placeholder="<?php echo $lang['PRG_MANDANT_PLZ_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-9">
                                    <input id="prg_ort_mandant" name="ort_mandant" placeholder="<?php echo $lang['PRG_MANDANT_ORT_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_LAND']; ?></small></label>
                                <div class="col-md-12">
                                    <select name="land_mandant" id="prg_land_mandant" class="form-control">
                                        <?php echo $countries; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_TELEFON']; ?>  </small></label>
                                <div class="col-md-12">
                                    <input id="prg_telefon_mandant" name="telefon_mandant" placeholder="<?php echo $lang['PRG_MANDANT_TELEFON_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_EMAIL']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_email_mandant" name="email_mandant" placeholder="<?php echo $lang['PRG_MANDANT_EMAIL_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_URL']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_url_mandant" name="url_mandant" placeholder="<?php echo $lang['PRG_MANDANT_URL_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_MANDANT_INFO']; ?></small></label>
                                <div class="col-md-12">
                                    <textarea id="prg_info_mandant" name="info_mandant" rows="6" class="form-control" placeholder="<?php echo $lang['PRG_MANDANT_INFO_PLACEHOLDER']; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="presse" class="tab-pane">
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_PRESSE_NAME']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_name_presse" type="text" name="name_presse" value="<?php echo isset($userData->name_presse) ? $userData->name_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_NAME_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4"></label>
                                <label class="col-md-4"><small><?php echo $lang['PRG_PRESSE_VORNAME']; ?></small></label>
                                <label class="col-md-4"><small><?php echo $lang['PRG_PRESSE_NACHNAME']; ?></small></label>
                                <div class="col-md-4">
                                    <select name="anrede_presse" id="prg_anrede_presse" class="form-control">
                                        <option value="0" <?php echo (isset($userData->anrede_presse) && $userData->anrede_presse == 0) ? 'selected="selected"' : ''; ?>><?php echo $lang['PRG_MRS']; ?></option>
                                        <option value="1" <?php echo (isset($userData->anrede_presse) && $userData->anrede_presse == 1) ? 'selected="selected"' : ''; ?>><?php echo $lang['PRG_MR']; ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_vorname_presse" type="text" name="vorname_presse" value="<?php echo isset($userData->vorname_presse) ? $userData->vorname_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_VORNAME_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_nachname_presse" type="text" name="nachname_presse" value="<?php echo isset($userData->nachname_presse) ? $userData->nachname_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_NACHNAME_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-9"><small><?php echo $lang['PRG_PRESSE_STRASSE']; ?></small></label>
                                <label class="col-md-3"><small><?php echo $lang['PRG_PRESSE_NUMMER']; ?></small></label>
                                <div class="col-md-9">
                                    <input id="prg_strasse_presse" type="text" name="strasse_presse" value="<?php echo isset($userData->strasse_presse) ? $userData->strasse_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_STRASSE_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <input id="prg_nummer_presse" type="text" maxlength="10" name="nummer_presse" value="<?php echo isset($userData->nummer_presse) ? $userData->nummer_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_NUMMER_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3"><small><?php echo $lang['PRG_PRESSE_PLZ']; ?></small></label>
                                <label class="col-md-9"><small><?php echo $lang['PRG_PRESSE_ORT']; ?></small></label>
                                <div class="col-md-3">
                                    <input id="prg_plz_presse" type="text" maxlength="10" name="plz_presse" value="<?php echo isset($userData->plz_presse) ? $userData->plz_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_PLZ_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                                <div class="col-md-9">
                                    <input id="prg_ort_presse" type="text" name="ort_presse" value="<?php echo isset($userData->ort_presse) ? $userData->ort_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_ORT_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_PRESSE_LAND']; ?></small></label>
                                <div class="col-md-12">
                                    <select name="land_presse" id="prg_land_presse" class="form-control">
                                        <?php echo $countries; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_PRESSE_TELEFON']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_telefon_presse" type="text" name="telefon_presse" value="<?php echo isset($userData->telefon_presse) ? $userData->telefon_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_TELEFON_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_PRESSE_EMAIL']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_email_presse" type="text" name="email_presse" value="<?php echo isset($userData->email_presse) ? $userData->email_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_EMAIL_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $lang['PRG_PRESSE_URL']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_url_presse" type="text" name="url_presse" value="<?php echo isset($userData->url_presse) ? $userData->url_presse : ''; ?>" placeholder="<?php echo $lang['PRG_PRESSE_URL_PLACEHOLDER']; ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottomPublishForm">
                    <div id="imgChose" class="col-md-12">
                        <h3>
                            <h3>
                                <span class="label label-primary">2</span>
                                <?php echo $lang['PRG_IMAGE']; ?>
                            </h3>
                        </h3>
                        <div class="row rowPics">
                            <div id="divImg1" class="col-xs-3 picWithRadio">
                                <div class="col-xs-12 pic">
                                    <label class="col-xs-12" for="img0"><img class="imgs" alt="placeholder" src="<?php echo plugins_url('../assets/images/placeholder.png', dirname(__FILE__)); ?>"></label>
                                </div>
                                <div class="text-center select col-xs-12">
                                    <input <?php echo ($images) ? '' : 'checked="checked"'; ?> id="img0" type="radio" name="bild" value="">
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
                                            <input <?php echo ($i == 1) ? 'checked="checked"' : ''; ?> class="newImg" id="img<?php echo $i + 1; ?>" type="radio" name="bild" value="<?php echo $image[1]; ?>">
                                        </div>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                        <div class="clear form-group">

                        </div>
                        <div id="img_rights">
                            <div class="col-md-12">
                                <div class="col-md-6 form-group">
                                    <label class="col-md-5"><small><?php echo $lang['PRG_IMAGE_TITLE']; ?></small></label>
                                    <div class="col-md-5">
                                        <input id="prg_bildtitel" name="bildtitel" placeholder="<?php echo $lang['PRG_IMAGE_TITLE_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <label class="col-md-5"><small><?php echo $lang['PRG_IMAGE_COPYRIGHT']; ?></small></label>
                                    <div class="col-md-5">
                                        <input id="prg_bildcopyright" name="bildcopyright" placeholder="<?php echo $lang['PRG_IMAGE_COPYRIGHT_PLACEHOLDER']; ?>" class="form-control" type="text" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="pm" name="channel">
                    <input type="hidden" value="createMessagePRG" name="action">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $userLang; ?>">
                    <input type="hidden" id="token" name="token" value="<?php echo (isset($_SESSION['b2s_prg_token']) && !empty($_SESSION['b2s_prg_token'])) ? $_SESSION['b2s_prg_token'] : 0; ?>">
                    <input type="hidden" id="prg_id" name="prg_id" value="<?php echo (isset($_SESSION['b2s_prg_id']) && !empty($_SESSION['b2s_prg_id'])) ? $_SESSION['b2s_prg_id'] : 0; ?>">
                    <input type="hidden" id="plugin_url" name="plugin_url" value="<?php echo plugins_url('', dirname(__FILE__)); ?>">
                    <div id="submitDiv">
                        <button type="submit" name="draft" value="1" class="btn btn-success userbtn draft checkPRGButton" disabled="disabled" data-color="primary"><?php echo $lang['PRG_DRAFT']; ?></button>
                        <button type="submit" name="publish" value="1" class="btn btn-success userbtn checkPRGButton" disabled="disabled" data-color="primary"><?php echo $lang['PRG_PUBLISH']; ?></button>
                    </div>
                </div>
            </fieldset>
            <div id="clearbot">

            </div>
        </form>
    </div>
</div>
