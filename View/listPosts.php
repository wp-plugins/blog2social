<?php
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

global $wpdb;

$currentUserID = get_current_user_id();
$filters = b2sgetFilterSent($currentUserID);
if (!empty($_GET['filterSet'])) {
    $allowedFilter = array(
        'prg',
        'twitter',
        'facebook',
        'linkedin',
        'googleplus',
        'tumblr',
    );

    if (in_array($_GET['filterSet'], $allowedFilter)) {
        $filters[$_GET['filterSet']] = !$filters[$_GET['filterSet']];
        b2ssetFilterSent($currentUserID, $filters);
    }
}

$sql = $wpdb->prepare("SELECT `lang` FROM `prg_connect_config` WHERE `author_id` = %d", $currentUserID);
$userExist = $wpdb->get_row($sql);

$postsPerPage = 25;
$currentPage = (int) isset($_GET['prgPage']) ? $_GET['prgPage'] : 1;

$addWhere = '';
if (in_array(true, $filters)) {
    $addWhere .= ' AND NOT (';
    foreach ($filters as $filter => $bool) {
        if ($bool) {
            $addWhere .= "`sent`.`" . $filter . "sent` IS NOT NULL AND ";
        }
    }
    $addWhere = substr($addWhere, 0, -5);
    $addWhere .= ')';
}

$addSearch = '';

if (isset($_GET['prgSearch'])) {
    $addSearch = $wpdb->prepare(' AND `post_title` LIKE %s', '%' . $_GET['prgSearch'] . '%');
}

$sqlPostsPage = "SELECT `$wpdb->posts`.`ID`, `post_author`, `post_date`, `post_title`, `sent`.`prgsent`, `sent`.`twittersent`, `sent`.`facebooksent` 
		FROM `$wpdb->posts` 
		LEFT JOIN `prg_connect_sent` AS `sent` ON `$wpdb->posts`.`ID` = `sent`.`wpid` 
		WHERE `post_status` = 'publish' AND `post_type` = 'post' AND `post_author` = $currentUserID $addWhere $addSearch
		ORDER BY `ID` DESC
		LIMIT " . (($currentPage - 1) * $postsPerPage) . ",$postsPerPage";
$posts_array = $wpdb->get_results($sqlPostsPage);

require_once dirname(__FILE__) . '/../Controller/preparePagination.php';

$sqlNumberPosts = "SELECT COUNT(*)
		FROM `$wpdb->posts`
		LEFT JOIN `prg_connect_sent` AS `sent` ON `$wpdb->posts`.`ID` = `sent`.`wpid`
		WHERE `post_status` = 'publish' AND post_type = 'post' AND `post_author` = $currentUserID $addWhere $addSearch";

$numberAllPosts = $wpdb->get_var($sqlNumberPosts);

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$userExist->lang];
?>
<noscript>
<div class="errorMeldungJS">
    <?php echo $text['JS_OUT']; ?>
</div>
<br>
</noscript>
<script type="text/javascript">
    var b2sUrlToPlugin = "<?php echo plugins_url('', dirname(__FILE__)); ?>";
</script>
<input type="hidden" name="lang" id="lang" value="<?php echo $userExist->lang; ?>" />
<div class="container" id="PRGfilter">
    <div id="prgLogo">
        <a href="http://www.pr-gateway.de" target="_blank">
            <img id="imgLarge" src="<?php echo plugins_url('/images/bannerb2s.png', dirname(__FILE__)); ?>">
        </a>
    </div>
    <div id="PRGSearch">
        <form id="search-form" class="navbar-form">
            <input type="hidden" name="page" value="prg_connect" id="PRGSearchInput">
            <div class="form-group" id="divSearchInput">
                <input class="form-control text-input" type="text" placeholder="<?php echo $text['SEARCH_TITLE']; ?>" name="prgSearch" id="inputSearch">
                <button class="btn btn-primary administrative" type="submit">
                    <img src="<?php echo plugins_url('/images/Black_Search.png', dirname(__FILE__)); ?>">
                </button>
            </div>

        </form>
    </div>
    <div class="divClear">
    </div>
    <div>		
        <div class="pull-right btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                Filter
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="?page=blog2social&filterSet=prg<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>">
                        <input onclick="window.open('?page=blog2social&filterSet=prg<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>', '_self');" <?php echo ($filters['prg']) ? 'checked="checked"' : '' ?> type="checkbox"> <?php echo $text['TO_PRG_SENT_MSG']; ?>
                    </a>
                </li>
                <li>
                    <a href="?page=blog2social&filterSet=twitter<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>">
                        <input onclick="window.open('?page=blog2social&filterSet=twitter<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>', '_self');" <?php echo ($filters['twitter']) ? 'checked="checked"' : '' ?> type="checkbox"> <?php echo $text['TO_TWITTER_SENT_MSG']; ?>
                    </a>
                </li>
                <li>
                    <a href="?page=blog2social&filterSet=facebook<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>">
                        <input onclick="window.open('?page=blog2social&filterSet=facebook<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>', '_self');" <?php echo ($filters['facebook']) ? 'checked="checked"' : '' ?> type="checkbox"> <?php echo $text['TO_FACEBOOK_SENT_MSG']; ?>
                    </a>
                </li>
                <li>
                    <a href="?page=blog2social&filterSet=linkedin<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>">
                        <input onclick="window.open('?page=blog2social&filterSet=linkedin<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>', '_self');" <?php echo ($filters['linkedin']) ? 'checked="checked"' : '' ?> type="checkbox"> <?php echo $text['TO_LINKEDIN_SENT_MSG']; ?>
                    </a>
                </li>
                <li>
                    <a href="?page=blog2social&filterSet=tumblr<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>">
                        <input onclick="window.open('?page=blog2social&filterSet=tumblr<?php echo!empty($_GET['prgSearch']) ? '&prgSearch=' . $_GET['prgSearch'] : ''; ?>', '_self');" <?php echo ($filters['tumblr']) ? 'checked="checked"' : '' ?> type="checkbox"> <?php echo $text['TO_TUMBLR_SENT_MSG']; ?>
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="pull-right btn btn-info administrative" id="listPostFBBtn" onclick="window.open('http://developer.pr-gateway.de/wp/feedback.php?lang=<?php echo $userExist->lang; ?>', 'Blog2Social Feedback', 'width=650,height=500,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20');">
            <?php echo $text['GIVE_FEEDBACK']; ?>
        </button>
    </div>
    <div class="divClear">

    </div>
</div>
<?php
if (empty($posts_array)) :
    echo $text['NO_ENTRIES'];
else :
    ?>
    <div id="postTable">
        <table id="Posts" class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $text['TITLE'] ?></th>
                    <th><?php echo $text['CREATED'] ?></th>
                    <th><?php echo $text['AUTHOR'] ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <?php
            foreach ($posts_array as $var) :
                ?>
                <tr>
                    <td><?php echo $var->ID; ?></td>
                    <td style="width:25%;"><?php echo $var->post_title; ?></td>
                    <td><?php echo date($text['DATE_FORM'], strtotime($var->post_date)); ?></td>
                    <td><?php echo the_author_meta('user_nicename', $var->post_author); ?></td>
                    <td>
                        <a title="<?php echo $text['MSG_TO_SN']; ?>" name="sendToSocial" id="<?php echo $var->ID; ?>" class="sendToSocial sendBtn">
                            <button class="btn btn-xs btn-info userbtn" type="button">
                                <?php echo $text['TRANSFER_TO_SN']; ?>
                            </button>
                        </a>
                    </td>
                    <td>
                        <a name="preparePost" id="blog-<?php echo $var->ID; ?>" class="preparePost sendBtn">
                            <button id="btnSendToPR" class="btn btn-xs btn-primary userbtn" data-color="primary" type="button">
                                <?php echo $text['TRANSFER_TO_PRG']; ?>
                            </button>
                        </a>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
        </table>
    </div>
<?php
endif;
?>
<div id="pagination" class="text-center">
    <?php
    echo preparePagination($numberAllPosts, $postsPerPage, $currentPage);
    ?>
</div>

<div class="clear">

</div>

<div id="prgFooter" class="pull-right">
    <a href="http://www.pr-gateway.de/impressum" target="_blank"><?php echo $text['IMPRINT']; ?></a>
    <a href="http://www.pr-gateway.de/datenschutz" target="_blank"><?php echo $text['POLICY']; ?></a>
</div>
