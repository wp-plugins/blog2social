<link rel="stylesheet" id="B2SBOOTCSS" href ="<?php echo plugins_url('assets/css/b2s.css', dirname(__FILE__)); ?>" type = "text/css" media = "all">
<div class="col-md-12 col-xs-12">
    <br>
    <?php if (!isset($showB2SExtension) || (isset($showB2SExtension) && $showB2SExtension !== false)) { ?>
        <div class="pull-left">
            <a target="_blank" href="http://service.blog2social.com">
                <img class="b2s-logo" style="width: 50%;" src="<?php echo plugins_url('/assets/images/b2s_logo.png', dirname(__FILE__)); ?>">
            </a>
        </div>
    <?php } ?>
    <br clear="both">
    <br clear="both">
    <div class="alert alert-danger"><?php echo (!isset($textError) || empty($textError)) ? $lang['ERROR_BAD_REQUEST'] : $textError; ?></div>
</div>