<?php
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

global $wpdb;

$currentUserID = get_current_user_id();

$sqlUserData = $wpdb->prepare("SELECT `UserBlogToken`,`lang` FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
$savedUser = $wpdb->get_row($sqlUserData);

require_once dirname(__FILE__) . '/../Helper/getWarrants.php';

$socials = WarrantsCheck::verifyCreds(substr($savedUser->UserBlogToken, 3));

$facebookName = isset($socials->{"1"}) ? $socials->{"1"} != false ? $socials->{"1"} : '' : '';
$twitterName = isset($socials->{"2"}) ? $socials->{"2"} != false ? $socials->{"2"} : '' : '';
$linkedinName = isset($socials->{"3"}) ? $socials->{"3"} != false ? $socials->{"3"} : '' : '';
$tumblrName = isset($socials->{"4"}) ? $socials->{"4"} != false ? $socials->{"4"} : '' : '';
$storifyName = isset($socials->{"5"}) ? $socials->{"5"} != false ? $socials->{"5"} : '' : '';
$pinterestName = isset($socials->{"6"}) ? $socials->{"6"} != false ? $socials->{"6"} : '' : '';
$flickrName = isset($socials->{"7"}) ? $socials->{"7"} != false ? $socials->{"7"} : '' : '';

$diigoName = isset($socials->{"9"}) ? $socials->{"9"} != false ? $socials->{"9"} : '' : '';
$googleplusName = isset($socials->{"10"}) ? $socials->{"10"} != false ? $socials->{"10"} : '' : '';

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$savedUser->lang];

?>
<div id="prgLogo">
    <a target="_blank" href="http://www.pr-gateway.de">
        <img id="imgLarge" src="<?php echo plugins_url('/images/bannerb2s.png', dirname(__FILE__)); ?>">
    </a>
</div>
<div class="pull-right">
    <button id="configFBBtn" type="button" class="pull-right btn btn-info administrative" onclick="window.open('http://developer.pr-gateway.de/wp/feedback.php?lang=<?php echo $savedUser->lang; ?>', 'Blog2Social Feedback', 'width=650,height=500,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20');">
        <?php echo $text['GIVE_FEEDBACK']; ?>
    </button>
</div>
<div class="clear">

</div>
<input type="hidden" name="lang" id="lang" value="<?php echo $savedUser->lang; ?>">
<div class="page-header">
    <h3>
        <?php echo $text['CFG_SN_HEADER']; ?>
    </h3>
</div>
<table id="Posts" class="configSocialTable table">
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.facebook.com" target="_blank">Facebook</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/facebook.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($facebookName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($facebookName)) {
                echo 'Account: ' . $facebookName .
                '<br><a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=1&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkFacebook" class="SocialNetwork connectSocials">' . $text['CHANGE_NETWORK'] . '</a>';
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=1&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkFacebook" class="SocialNetwork connectSocials">' . $text['CONN_FACEBOOK'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.twitter.com" target="_blank">Twitter</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('/images/twitter.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td>
            <?php
            if (empty($twitterName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($twitterName)) {
                echo 'Account: ' . $twitterName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=2&userid=' . substr($savedUser->UserBlogToken, 3) . '" id="SocialNetworkTwitter" class="SocialNetwork connectSocials">' . $text['CONN_TWITTER'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.linkedin.com" target="_blank">LinkedIn</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/linkedin.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($linkedinName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($linkedinName)) {
                echo 'Account: ' . $linkedinName;
            } else {

                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=3&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkLinkedin" class="SocialNetwork connectSocials">' . $text['CONN_LINKEDIN'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.tumblr.com" target="_blank">Tumblr</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/tumblr.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($tumblrName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($tumblrName)) {
                echo 'Account: ' . $tumblrName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=4&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkTumblr" class="SocialNetwork connectSocials">' . $text['CONN_TUMBLR'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.storify.com" target="_blank">Storify</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/storify.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($storifyName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($storifyName)) {
                echo 'Account: ' . $storifyName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=5&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkStorify" class="SocialNetwork connectSocials">' . $text['CONN_STORIFY'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.pinterest.com" target="_blank">Pinterest</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/pinterest.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($pinterestName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($pinterestName)) {
                echo 'Account: ' . $pinterestName .
                '<br><a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=6&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkPinterest" class="SocialNetwork connectSocials">' . $text['CHANGE_NETWORK'] . '</a>';
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=6&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkPinterest" class="SocialNetwork connectSocials">' . $text['CONN_PINTEREST'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.flickr.com" target="_blank">Flickr</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/flickr.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($flickrName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($flickrName)) {
                echo 'Account: ' . $flickrName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=7&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkFlickr" class="SocialNetwork connectSocials">' . $text['CONN_FLICKR'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr class="socialSoonFirst">
        <td>
            <a class="SocialNetwork" href="http://www.xing.com" target="_blank">Xing</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/xing.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
        </td>
        <td class="socialMeths">
            Coming soon
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.diigo.com" target="_blank">Diigo</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/diigo.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($diigoName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($diigoName)) {
                echo 'Account: ' . $diigoName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=9&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkDiigo" class="SocialNetwork connectSocials">' . $text['CONN_DIIGO'] . '</a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a class="SocialNetwork" href="http://www.plus.google.com" target="_blank">Google+</a>
        </td>
        <td>
            <img src="<?php echo plugins_url('images/googleplus.png', dirname(__FILE__)); ?>" class="socialConfigLogo">
        </td>
        <td class="socialState">
            <?php
            if (empty($googleplusName)) :
                ?>
                <input type="button" disabled="disabled" class="btn btn-danger" value="<?php echo $text['DEACTIVE']; ?>">
                <?php
            else :
                ?>
                <input type="button" disabled="disabled" class="btn btn-success" value="<?php echo $text['ACTIVE']; ?>">
            <?php
            endif;
            ?>
        </td>
        <td class="socialMeths">
            <?php
            if (!empty($googleplusName)) {
                echo 'Account: ' . $googleplusName;
            } else {
                echo '<a href="http://developer.pr-gateway.de/wp/index.php?action=register&type=10&userid=' . substr($savedUser->UserBlogToken, 3) . '" target="_blank" id="SocialNetworkGoogleplus" class="SocialNetwork connectSocials">' . $text['CONN_GOOGLEPLUS'] . '</a>';
            }
            ?>
        </td>
    </tr>
</table>

<div id="prgFooter" class="pull-right">
    <a href="http://www.pr-gateway.de/impressum" target="_blank"><?php echo $text['IMPRINT']; ?></a>
    <a href="http://www.pr-gateway.de/datenschutz" target="_blank"><?php echo $text['POLICY']; ?></a>
</div>