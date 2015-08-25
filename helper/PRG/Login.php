<?php
if (!isset($_GET['postId']) || (int) $_GET['postId'] == 0) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Error.php';
}else{
?>
<br>
<div class="col-md-12 col-xs-12">
    <div class="pull-left">
        <a target="_blank" href="http://www.pr-gateway.de">
            <img class="prg-logo" src="<?php echo plugins_url('../assets/images/prg_logo.png', dirname(__FILE__)); ?>">
        </a>
    </div>
</div>
<br class="clear">

<?php if(B2SUPATE == 1) { ?>
    <br>
    <div class="col-md-12 col-xs-12">    
        <div class="text-center alert alert-danger"><?php echo $lang['B2SUPDATE_INFO']; ?></div>
        <br class="clear">
    </div>
<?php } else{ ?>

<noscript><div class="col-md-12 col-xs-12"> <div class="alert alert-danger text-center"><h2><?php echo $lang['VIEW_JS']; ?></h2></div></div></noscript>

<div style="display:none;" class="col-md-12 col-xs-12" id="b2sLoader">
    <br>
    <div class="text-center">
        <img src="<?php echo plugins_url('../assets/images/b2s_loading.gif', dirname(__FILE__)); ?>">
        <h3><?php echo $lang['LOADING']; ?></h3>
    </div>
</div>

<div style="display:none;" class="loginPRGWarning col-md-12 col-xs-12">
    <br>
    <div class="text-center">
        <div class="alert alert-danger"><?php echo $lang['PRG_LOGIN_WARNING']; ?></div>
    </div>
</div>

<div style="display:none;" class="loginPRGDanger col-md-12 col-xs-12">
    <br>
    <div class="text-center">
        <div class="alert alert-danger"><?php echo $lang['PRG_LOGIN_DANGER']; ?></div>
    </div>
</div>


<input type="hidden" name="lang" id="lang" value="<?php echo substr(B2SLANGUAGE, 0, 2); ?>">
<input type='hidden' id='plugin_url' value='<?php echo plugins_url('', dirname(__FILE__)); ?>'>
<input type="hidden" name="postId" id="postId" value="<?php echo (int)$_GET['postId']; ?>">

<div class="loginPRG col-md-12 col-xs-12">
    <form method="POST" id="loginPRG" enctype="multipart/form-data">
        <div class="col-md-4">
            <div class="form-group">
                <label><small><?php echo $lang['PRG_LOGIN_USERNAME']; ?></small></label>
                <input type="text" name="username" id="username" placeholder="<?php echo $lang['PRG_LOGIN_USERNAME_PLACEHOLDER']; ?>" class="form-control" required="">
            </div>
            <div class="form-group">
                <label><small><?php echo $lang['PRG_LOGIN_PASSWORD']; ?></small></label>
                <input type="password" name="password" id="password" placeholder="<?php echo $lang['PRG_LOGIN_PASSWORD_PLACEHOLDER']; ?>" class="form-control" required="">
            </div>
            <input type="hidden" name="action" value="loginPRG" />
            <input type="hidden" name="token" value="<?php echo base64_encode(time()); ?>"/>
            <button class="btn btn-success" id="loginPRGButton" type="submit" data-color="primary"><?php echo $lang['PRG_LOGIN_BUTTON']; ?></button>		
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-6">
            <br>
            <div class="well well-sm">
                <?php echo $lang['PRG_LOGIN_INFO']; ?>
                <br><br>
                <a href="http://prg.li/pr-gateway-connect-registration" target="_blank" class="btn btn-primary btn-xs"> <?php echo $lang['PRG_REGISTER']; ?></a>
            </div>
        </div>
    </form>
</div>
<?php } } ?>