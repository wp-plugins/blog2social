jQuery(document).ready(function () {

    jQuery(".openPRGCSocialMediaPoster").click(function () {
        autosave();
        var blogid = jQuery("#post_ID").attr("value");
        jQuery.fancybox({
            type: "iframe",
            autoDimensions: false,
            titleShow: false,
            href: "admin.php?prgPluginExtern=sendSocialNetworks&blogid=" + blogid,
            width: "80%",
            height: "90%",
            scrolling: "auto"
        });
    });

    if(jQuery("#original_post_status").val() === "publish") {
        jQuery("#b2swidgetSNPoster").show();
    } else {
        jQuery("#b2swidgetSNPublishFirst").show();
    }
});
