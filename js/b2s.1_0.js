jQuery.noConflict();
jQuery(document).ready(function () {
    if (jQuery("#lang").val() == "de") {
        var validateMessages = {
            user: "Bitte Benutzername eingeben",
            pass: "Bitte Passwort eingeben",
            fehler: "Fehler",
            falsche_daten: "Falsche Daten"
        };
    } else if (jQuery("#lang").val() == "en") {
        var validateMessages = {
            user: "Please set Username",
            pass: "Please set Password",
            fehler: "Error",
            falsche_daten: "Wrong data"
        };
    }

    jQuery(".sendToSocial").click(function () {
        var blogid = jQuery(this).attr("id");
        jQuery.fancybox({
            type: "iframe",
            autoDimensions: false,
            titleShow: false,
            href: "?prgPluginExtern=sendSocialNetworks&blogid=" + blogid,
            width: "80%",
            height: "90%",
            scrolling: "auto"
        });
    });
    jQuery(".preparePost").click(function () {
        var blogid = jQuery(this).attr("id");
        jQuery.ajax({
            url: b2sUrlToPlugin + "/Controller/sessionCheck.php",
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data["sessCheck"] === true) {
                    jQuery.fancybox({
                        type: "iframe",
                        autoDimensions: false,
                        titleShow: false,
                        href: "?prgPluginExtern=sendPress&blogid=" + blogid,
                        width: "100%",
                        height: "80%",
                        scrolling: "auto"
                    });
                }
                else {
                    jQuery.fancybox({
                        type: "ajax",
                        href: "?prgPluginExtern=Login",
                        scrolling: "no",
                        padding: 0,
                        onComplete: function () {
                            jQuery("#prg_login").validate({
                                rules: {
                                    user: "required",
                                    pass: "required"
                                },
                                messages: validateMessages,
                                submitHandler: function (form) {
                                    jQuery('#btnLogin').attr("disabled", "diabled");
                                    jQuery.ajax({
                                        url: b2sUrlToPlugin + "/Controller/PRGlogin.php",
                                        type: "POST",
                                        dataType: "json",
                                        cache: false,
                                        data: jQuery(form).serialize(),
                                        success: function (data) {
                                            jQuery('#btnLogin').removeAttr("disabled");
                                            if (data['status'] == false) {
                                                alert(validateMessages.falsche_daten);
                                                return false;
                                            } else if (data['status'] == "error") {
                                                alert(validateMessages.falsche_daten);
                                                return false;
                                            } else {
                                                jQuery("#toplevel_page_prg_connect .wp-submenu").append('<li><a href="admin.php?page=b2slogout">Logout</a></li>');
                                                jQuery("#" + blogid).trigger("click");
                                            }
                                            return false;
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });
    });
    jQuery(".connectSocials").click(function () {
        var url = jQuery(this).attr("href");
        var win = window.open(url, "Register Social", 'width=650,height=500,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20');
        var pollTimer = window.setInterval(function () {
            if (win.closed !== false) {
                window.clearInterval(pollTimer);
                parent.location = parent.location.href.split("&status")[0];
            }
        }, 1000);
        return false;
    });
});