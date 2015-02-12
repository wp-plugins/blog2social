<?php
if (!function_exists('add_action')) {
    echo '<div class="page-header"><h2>Hi there!  I\'m just a plugin</h2></div><p>, not much I can do when called directly.</p>';
    exit;
}

if (!isset($_SESSION['prg_id'])) {
    echo '<div class="page-header"><h2>PR-Gateway Login <small>erforderlich</small></h2></div><p>Um Ihre Mitteilung an PR-Gateway zu übermitteln und anschließend an Portale zu veröffentlichen müssen Sie sich zuerst bei PR-Gateway anmelden.</p>';
    exit;
}

require_once dirname(__FILE__) . '/../Helper/getWpImages.php';

$page_data = get_page(substr($_GET['blogid'], 5));
$currentUserID = get_current_user_id();

global $wpdb;

$sqlUserData = $wpdb->prepare("SELECT * FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
$savedUser = $wpdb->get_row($sqlUserData);


$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$lang = 'titel_' . $savedUser->lang;
$text = $textAll[$savedUser->lang];

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
curl_setopt($ch, CURLOPT_URL, 'http://developer.pr-gateway.de/wp/get.php?action=getCategory');
$katsXml = simplexml_load_string(curl_exec($ch));

$kats = '';
foreach ($katsXml as $val) {
    $kats .= '<option value="' . $val->id . '">' . $val->$lang . '</option>' . PHP_EOL;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
curl_setopt($ch, CURLOPT_URL, 'http://developer.pr-gateway.de/wp/get.php?action=getCountry');
$countriesXml = simplexml_load_string(curl_exec($ch));

$countries = '';
foreach ($countriesXml as $val) {
    echo $val->lang;
    $countries .= '<option value="' . $val->tag . '"';
    if ($val->tag == $savedUser->land) {
        $countries .= ' selected="selected"';
    }
    $countries .= '>' . $val->$lang . '</option>' . PHP_EOL;
}

$images = getImagesByPostID($page_data->ID, false);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
        <title><?php echo $text['SEND_PRG_HEADER']; ?></title>
        <link rel = "stylesheet" id = "Bootstrap-css" href = "<?php echo plugins_url('css/bootstrap.min.3_0_3.css', dirname(__FILE__)); ?>" type = "text/css" media = "all">
        <link rel = "stylesheet" id = "PRG-Form-css" href = "<?php echo plugins_url('css/b2s_form.1_0.css', dirname(__FILE__)); ?>" type = "text/css" media = "all">
        <?php wp_print_scripts('jquery'); ?>
        <script type="text/javascript" src="<?php echo plugins_url('js/b2s_form.1_0.js', dirname(__FILE__)); ?>"></script>
        <script type="text/javascript" src="<?php echo plugins_url('js/bootstrap.min.3_0_3.js', dirname(__FILE__)); ?>"></script>
        <script type="text/javascript" src="<?php echo plugins_url('js/jquery.validate.min.1_9.js', dirname(__FILE__)); ?>"></script>
        <link rel="stylesheet" id="FancyBoxStyle-css"  href="<?php echo plugins_url('css/jquery.fancybox-1_3_4.css', dirname(__FILE__)); ?>" type="text/css" media="all">
        <script type="text/javascript" src="<?php echo plugins_url('js/jquery.fancybox-1_3_4.pack.js', dirname(__FILE__)); ?>"></script>
    </head>
    <body>
        <input type="hidden" name="lang" id="lang" value="<?php echo $savedUser->lang; ?>">
        <form method="POST" id="prgConnect_sendPress" enctype="multipart/form-data">
            <fieldset>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <h3>
                            <span class="label label-primary administrative-label"><?php echo $text['WRITE_MSG']; ?></span>
                        </h3>
                    </div>
                    <div class="col-md-4">
                        <h3>
                            <span id="clientdata" class="label label-primary administrative-label"><?php echo $text['GIVE_CONTACT']; ?></span>
                        </h3>
                    </div>
                </div>
                <div id="leftPublishForm">
                    <div class="form-group">
                        <label class="col-md-6"><small><?php echo $text['CATEGORY']; ?></small></label>
                        <label class="col-md-5"><small><?php echo $text['LANG_CHOOSE']; ?></small></label>
                        <div class="col-md-6">
                            <select name="kategorie_id" id="prg_cat" class="form-control">
                                <?php echo $kats; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select name="sprache" id="sprache" class="form-control">
                                <option value="DE"><?php echo $text['GERMAN']; ?></option>
                                <option value="EN"><?php echo $text['ENGLISH']; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['TITLE']; ?></small></label>
                        <div class="col-md-11">
                            <input id="prg_title" name="title" maxlength="150" placeholder="<?php echo $text['RCMD_LENGHT']; ?>" class="form-control" type="text" value="<?php echo $page_data->post_title ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['SUB_TITLE']; ?> <small>(optional)</small></small></label>
                        <div class="col-md-11">
                            <input id="prg_subline" name="subline" placeholder="<?php echo $text['SUB_TITLE']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['VIDEO']; ?>  <small>(optional)</small></small></label>
                        <div class="col-md-11">
                            <input id="prg_vidlink" name="video_link" placeholder="<?php echo $text['VIDEO']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['MSG_TEXT']; ?></small></label>
                        <div class="col-md-11">                     
                            <textarea id="prg_text" name="message" rows="10" data-provide="markdown" class="form-control" placeholder="<?php echo $text['MSG_TEXT']; ?>"><?php echo strip_shortcodes(strip_tags(trim($page_data->post_content)),'<a>'); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['KEYWORDS']; ?> <small>(optional)</small></small></label>
                        <div class="col-md-11">                     
                            <input id="prg_keywords" name="keywords" maxlength="200" placeholder="<?php echo $text['KEYWORDS']; ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-11"><small><?php echo $text['SHORT_TEXT']; ?> <small>(optional)</small></small></label>
                        <div class="col-md-11">                     
                            <textarea id="prg_short" name="shorttext" rows="4" class="form-control" placeholder="<?php echo $text['SHORT_TEXT']; ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div id="rightPublishForm">
                    <div class="col-md-12">
                        <ul id="formContact" class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#mandant"><?php echo $text['COMPANY']; ?></a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#presse"><?php echo $text['PRESS_CONTACT']; ?></a>
                            </li>
                        </ul>
                    </div>
                    <div id="myTabContent" class="tab-content">
                        <div id="mandant" class="tab-pane active">
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['COMPANY_NAME']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_firm_name" name="name_mandant" placeholder="<?php echo $text['COMPANY_NAME']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4"></label>
                                <label class="col-md-4"><small><?php echo $text['FIRST_NAME']; ?></small></label>
                                <label class="col-md-4"><small><?php echo $text['LAST_NAME']; ?></small></label>
                                <div class="col-md-4">
                                    <select name="anrede_mandant" id="prg_firm_andrede" class="form-control">
                                        <option value="0"><?php echo $text['MRS']; ?></option>
                                        <option value="1"><?php echo $text['MR']; ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_firm_partner" name="vorname_mandant" placeholder="<?php echo $text['FIRST_NAME']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_firm_partner2" name="nachname_mandant" placeholder="<?php echo $text['LAST_NAME']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-9"><small><?php echo $text['STR']; ?></small></label>
                                <label class="col-md-3"><small><?php echo $text['NUMBER']; ?></small></label>
                                <div class="col-md-9">
                                    <input id="prg_firm_add" name="strasse_mandant" placeholder="<?php echo $text['STR']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-3">
                                    <input id="prg_firm_add2" name="nummer_mandant" placeholder="<?php echo $text['NUMBER']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3"><small><?php echo $text['ZIP']; ?></small></label>
                                <label class="col-md-9"><small><?php echo $text['CITY']; ?></small></label>
                                <div class="col-md-3">
                                    <input id="prg_firm_pc" name="plz_mandant" maxlength="5" placeholder="<?php echo $text['ZIP']; ?>" class="form-control" type="text" value="">
                                </div>
                                <div class="col-md-9">
                                    <input id="prg_firm_city" name="ort_mandant" placeholder="<?php echo $text['CITY']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['COUNTRY']; ?></small></label>
                                <div class="col-md-12">
                                    <select name="land_mandant" id="prg_firm_state" class="form-control">
                                        <?php echo str_replace('selected="selected"', '', $countries); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['PHONE']; ?>  <small>(optional)</small></small></label>
                                <div class="col-md-12">
                                    <input id="prg_firm_tel" name="telefon_mandant" placeholder="<?php echo $text['PHONE']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['EMAIL_WITH_HINT']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_firm_email" name="email_mandant" placeholder="<?php echo $text['EMAIL']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['WEBSITE_WITH_HINT']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_firm_url" name="url_mandant" placeholder="<?php echo $text['LINK_TO_WEBSITE']; ?>" class="form-control" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['COMPANY_DESC']; ?></small></label>
                                <div class="col-md-12">
                                    <textarea id="prg_firm_descr" name="info_mandant" rows="6" class="form-control" placeholder="<?php echo $text['DESC']; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="presse" class="tab-pane">
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['AGENCY']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_presse" type="text" name="name_presse" value="<?php echo $savedUser->presse; ?>" placeholder="<?php echo $text['AGENCY']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4"></label>
                                <label class="col-md-4"><small><?php echo $text['FIRST_NAME']; ?></small></label>
                                <label class="col-md-4"><small><?php echo $text['LAST_NAME']; ?></small></label>
                                <div class="col-md-4">
                                    <select name="anrede_presse" id="prg_firm_andrede" class="form-control">
                                        <option value="0" <?php echo $savedUser->anrede == 0 ? 'selected="selected"' : ''; ?>><?php echo $text['MRS']; ?></option>
                                        <option value="1" <?php echo $savedUser->anrede == 1 ? 'selected="selected"' : ''; ?>><?php echo $text['MR']; ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_fname2" type="text" name="vorname_presse" value="<?php echo $savedUser->fname; ?>" placeholder="<?php echo $text['FIRST_NAME']; ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <input id="prg_lname2" type="text" name="nachname_presse" value="<?php echo $savedUser->lname; ?>" placeholder="<?php echo $text['LAST_NAME']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-9"><small><?php echo $text['STR']; ?></small></label>
                                <label class="col-md-3"><small><?php echo $text['NUMBER']; ?></small></label>
                                <div class="col-md-9">
                                    <input id="prg_address2" type="text" name="strasse_presse" value="<?php echo $savedUser->address; ?>" placeholder="<?php echo $text['STR']; ?>" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <input id="prg_address2" type="text" name="nummer_presse" value="<?php echo $savedUser->nummer; ?>" placeholder="<?php echo $text['NUMBER']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3"><small><?php echo $text['ZIP']; ?></small></label>
                                <label class="col-md-9"><small><?php echo $text['CITY']; ?></small></label>
                                <div class="col-md-3">
                                    <input id="prg_pc2" type="text" name="plz_presse" value="<?php echo $savedUser->pc; ?>" placeholder="<?php echo $text['ZIP']; ?>" class="form-control">
                                </div>
                                <div class="col-md-9">
                                    <input id="prg_city2" type="text" name="ort_presse" value="<?php echo $savedUser->city; ?>" placeholder="<?php echo $text['CITY']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['COUNTRY']; ?></small></label>
                                <div class="col-md-12">
                                    <select name="land_presse" id="prg_firm_state" class="form-control">
                                        <?php echo $countries; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['PHONE']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_phone2" type="text" name="telefon_presse" value="<?php echo $savedUser->phone; ?>" placeholder="<?php echo $text['PHONE']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['EMAIL']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_email2" type="text" name="email_presse" value="<?php echo $savedUser->email; ?>" placeholder="<?php echo $text['EMAIL']; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><small><?php echo $text['WEBSITE']; ?></small></label>
                                <div class="col-md-12">
                                    <input id="prg_www2" type="text" name="url_presse" value="<?php echo $savedUser->www; ?>" placeholder="<?php echo $text['WEBSITE']; ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottomPublishForm">
                    <div id="imgChose" class="col-md-12">
                        <h3>
                            <span class="label label-primary administrative-label"><?php echo $text['IMG_CHOOSE']; ?></span>
                        </h3>
                        <div class="row rowPics">
                            <div id="divImg1" class="col-xs-3 picWithRadio">
                                <div class="col-xs-12 pic">
                                    <label class="col-xs-12" for="img0"><img class="imgs" alt="placeholder" src="<?php echo plugins_url('/images/placeholder.png', dirname(__FILE__)); ?>"></label>
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
                                    <label class="col-md-5"><small><?php echo $text['PIC_TITLE']; ?></small></label>
                                    <div class="col-md-5">
                                        <input id="prg_pictitle" name="bildtitel" placeholder="<?php echo $text['PIC_NAME']; ?>" class="form-control" type="text" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <label class="col-md-5"><small><?php echo $text['PIC_OWNER']; ?></small></label>
                                    <div class="col-md-5">
                                        <input id="prg_owner" name="bildcopyright" placeholder="<?php echo $text['PIC_OWNER']; ?>" class="form-control" type="text" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="prg_send" value="1">
                    <input type="hidden" name="prg_isjs" value="0">
                    <input type="hidden" value="" name="message_id">
                    <input type="hidden" value="pm" name="channel">
                    <input type="hidden" value="<?php echo $page_data->ID; ?>" name="blogid">
                    <input type="hidden" value="<?php echo $_SESSION['prg_id']; ?>" name="user_id">
                    <input type="hidden" value="direct_insert_news" name="action">
                    <div id="submitDiv">
                        <button type="submit" name="draft" value="1" class="btn btn-success userbtn cancel" data-color="primary"><?php echo $text['SAVE_MSG_DRAFT']; ?></button>
                        <button type="submit" name="publish" value="1" class="btn btn-success userbtn" data-color="primary"><?php echo $text['PUBLISH_MSG']; ?></button>
                    </div>
                </div>
            </fieldset>
            <div id="clearbot">

            </div>
        </form>
    </body>
</html>