<?php
if (strlen($_POST['comment']) > 220) {
    $text = substr($_POST['comment'], 0, 220) . '... <span id="facebookSeeMore">See more</span>';
} else {
    $text = $_POST['comment'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Facebook Preview</title>
        <link rel="stylesheet"  href="<?php echo plugins_url('css/facebookPreview.css', dirname(__FILE__)); ?>" type="text/css" media="all" />
    </head>
    <body>
        <div id="facebookContainer">
            <div id="facebookHead">
                <img class="headObject" id="profilePic" src="<?php echo plugins_url('images/fbpicture.jpg', dirname(__FILE__)); ?>">
                <div class="headObject" id="userShared">
                    <p><?php echo '<span id="facebookUrl">' . $_POST['username'] . '</span> shared a link.'; ?></p>
                </div>
            </div>
            <div id="facebookContent">
                <?php echo $text ?>
            </div>
            <div id="facebookDemo">
                <?php
                if (!empty($_POST['imgSrc'])) :
                    ?>
                    <img id="demoImg" src="<?php echo $_POST['imgSrc'] ?>">
                <?php endif; ?>
                <div id="demoContent">
                    <span id="demoTitle"><?php echo $_POST['title'] ?></span><br>
                    <span id="demoUrl"><?php echo home_url(); ?></span>
                    <div id="demoText">
                        <?php echo substr($text, 0, 120) ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="facebookFeedbackBar">
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
    </body>
</html>