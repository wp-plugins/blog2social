<?php

$textAll = parse_ini_file(dirname(__FILE__) . '/../languages/lang.ini', TRUE);
$text = $textAll[$userExist->lang];
?>
<div id="prgLogoLogin">
    <a target="_blank" href="http://www.pr-gateway.de"><img id="imgLargeLogin" class="prgLoginImg" src="<?php echo plugins_url('/images/bannerb2s.png', dirname(__FILE__)); ?>"></a>
</div>
<div class="well well-sm" id="loginInfo">
    <small>
        <?php echo $text['INFOPRG']; ?>
    </small>
</div>
<div class="clear"></div>
<input type="hidden" name="lang" id="lang" value="<?php echo $userExist->lang ?>" />
<form method="POST" id="prg_login" class="form-horizontal">
    <div id="leftLoginForm">
        <div class="form-group">
            <label class="col-md-12"><small><?php echo $text['USERNAME']; ?></small></label>
            <div class="col-md-12">
                <input type="text" name="user" id="user" placeholder="<?php echo $text['USERNAME']; ?>" class="form-control" required="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-12"><small><?php echo $text['PASSWORD']; ?></small></label>
            <div class="col-md-12">
                <input type="password" name="pass" id="pass" placeholder="<?php echo $text['PASSWORD']; ?>" class="form-control" required="">
            </div>
        </div>
    </div>
    <div id="rightLoginForm">
        <div class="form-group">
            <div class="col-md-12" id="divBtnRegister">
                <button class="btn btn-xs btn-primary administrative" data-color="primary" type="button" onclick="window.open('http://prg.li/pr-gateway-connect-registration', 'Registrierung');">
                    <?php echo $text['REGISTER']; ?>
                </button>
            </div>
            <div id="divBtnLogin" class="col-md-12">
                <button id="btnLogin" class="btn btn-success" type="submit" data-color="primary"><?php echo $text['LOGIN']; ?></button>					
            </div>
        </div>
    </div>
</form>