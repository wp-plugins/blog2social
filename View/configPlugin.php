<?php
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

$currentUserID = get_current_user_id();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.pr-gateway.de/version.b2s.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$resultVersion = curl_exec($ch);

$resultVersionArr = explode('#', $resultVersion);

global $wpdb;

if (isset($_POST['prgPlugin_save']) && $_POST['prgPlugin_save'] == '1') {
    if ($_POST['lang'] == 'de' || $_POST['lang'] == 'en') {
        $sqlUpdateData = $wpdb->prepare("UPDATE `prg_connect_config` SET `lang` = %s WHERE `author_id` = %d", $_POST['lang'], $currentUserID);
        $wpdb->query($sqlUpdateData);
        $userConfig['lang'] = $_POST['lang'];
        $userConfig = (object) $userConfig;
        echo '<meta http-equiv="refresh" content="0; URL=?page=b2spluginconfig">';
        exit;
    }
} else {
    $sql = $wpdb->prepare("SELECT `lang` FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
    $userConfig = $wpdb->get_row($sql);
}

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$userConfig->lang];

if ($resultVersionArr[0] != PLUGINVERS) :
    ?>
    <br>
    <div class="alert alert-danger text-center">
        <?php echo '<strong>' . $text['OUTDATED'] . '</strong>'; ?>
    </div>

    <?php
    return;
endif;
?>

<div id="prgLogo">
    <a href="http://www.pr-gateway.de" target="_blank"><img id="imgLarge" src="<?php echo plugins_url('/images/bannerb2s.png', dirname(__FILE__)); ?>"></a>
</div>
<div class="clear">

</div>
<input type="hidden" name="lang" id="lang" value="<?php echo $userConfig->lang; ?>" />
<div class="page-header">
    <h3>
        <?php echo $text['SETTINGS']; ?>
    </h3>
</div>

<div id="configPage">
    <div id="configForm">
        <form method="POST" id="prg_cfgForm" class="form-horizontal">
            <div class="form-group">
                <label class="col-md-12"><small><?php echo $text['LANG_CHOOSE']; ?></small></label>
                <div class="col-md-12">
                    <select name="lang">
                        <option <?php echo $userConfig->lang == 'en' ? 'selected="selected"' : '' ?> value="en">
                            <?php echo $text['ENGLISH']; ?>
                        </option>
                        <option <?php echo $userConfig->lang == 'de' ? 'selected="selected"' : '' ?> value="de">
                            <?php echo $text['GERMAN']; ?>
                        </option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="prgPlugin_save" value="1"/>
            <span class="pull-right">
                <button id="btnSubmit" class="btn btn-primary administrative" type="submit" data-color="primary"><?php echo $text['SAVE']; ?></button>
            </span>
        </form>
    </div>
</div>
<div class="clear">

</div>

<div id="prgFooter" class="pull-right">
    <a href="http://www.pr-gateway.de/impressum" target="_blank"><?php echo $text['IMPRINT']; ?></a>
    <a href="http://www.pr-gateway.de/datenschutz" target="_blank"><?php echo $text['POLICY']; ?></a>
</div>

