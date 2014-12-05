jQuery(document).ready(function () {
    if (jQuery("#lang").val() == "de") {
        var validateMessages = {
            prg_twitter: "Die Nachricht darf maximal 140 Zeichen lang sein",
            prg_linkedin: "Die Nachricht darf maximal 600 Zeichen lang sein",
            prg_pinterest: "Die Nachricht darf maximal 500 Zeichen lang sein",
            prg_xing: "Die Nachricht darf maximal 420 Zeichen lang sein",
            prg_diigo: "Die Nachricht darf maximal 250 Zeichen lang sein",
            bereits_veroeffentlicht: "Dieser Beitrag wurde auf Ihrem Profil bereits veröffentlicht",
            zuerstVeroeffentlichen: "Der Beitrag muss zuerst veröffentlicht werden, bevor er an soziale Netzwerke übermittelt werden kann"
        };
    } else if (jQuery("#lang").val() == "en") {
        var validateMessages = {
            prg_twitter: "max. 140 characters",
            prg_linkedin: "max. 600 characters",
            prg_pinterest: "max. 500 characters",
            prg_xing: "max. 420 characters",
            prg_diigo: "max. 250 characters",
            bereits_veroeffentlicht: "This post has already been published on your profile",
            zuerstVeroeffentlichen: "Your Post has to be published before transfered to social Networks"
        };
    }

    if (jQuery("#closeFB").val() == "1") {
        openInfoBox('<div class="alert alert-danger">' + validateMessages.zuerstVeroeffentlichen + '</div>');
    }

    if (jQuery("#checkTwitter").prop('checked')) {
        jQuery("#countdownTwit").show();
    } else {
        jQuery("#countdownTwit").hide();
    }

    if (jQuery("#checkLinkedin").prop('checked')) {
        jQuery("#countdownLink").show();
    } else {
        jQuery("#countdownLink").hide();
    }

    if (jQuery("#checkPinterest").prop('checked')) {
        jQuery("#countdownPint").show();
    } else {
        jQuery("#countdownPint").hide();
    }

    if (jQuery("#checkXing").prop('checked')) {
        jQuery("#countdownXing").show();
    } else {
        jQuery("#countdownXing").hide();
    }

    if (jQuery("#checkDiigo").prop('checked')) {
        jQuery("#countdownDiigo").show();
    } else {
        jQuery("#countdownDiigo").hide();
    }

    jQuery(document).on("keyup", '#prg_twitter', function () {
        var twitOfficial = 140;
        if (jQuery("#urlForTwit").val() != '') {
            twitOfficial = 139;
        }

        var maxChars = twitOfficial - jQuery("#urlForTwit").val().length;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_twitter').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_twitter').val().substring(maxChars, rest);
        secondtext = secondtext + '</span>\n\
\n\
<span id="urlTwit"><a id="aTwit" href="' + jQuery("#urlForTwit").val() + '">' + jQuery("#urlForTwit").val() + '</a></span>';
        jQuery('#preTwitter').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining < 0) {
            remaining = '0';
        }
        jQuery("#countdownTwit").html(remaining);
    });

    jQuery(document).on("keydown", '#prg_twitter', function () {
        var twitOfficial = 140;
        if (jQuery("#urlForTwit").val() != '') {
            twitOfficial = 139;
        }
        var maxChars = twitOfficial - jQuery("#urlForTwit").val().length;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_twitter').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_twitter').val().substring(maxChars, rest);
        secondtext = secondtext + '</span>\n\
\n\
<span id="urlTwit"><a id="aTwit" href="' + jQuery("#urlForTwit").val() + '">' + jQuery("#urlForTwit").val() + '</a></span>';
        jQuery('#preTwitter').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownTwit").html(remaining);
    });

    jQuery(document).on("keyup", '#prg_linkedin', function () {
        var maxChars = 600;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_linkedin').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_linkedin').val().substring(maxChars, rest);
        jQuery('#preLinkedin').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownLink").html(remaining);
    });

    jQuery(document).on("keydown", '#prg_linkedin', function () {
        var maxChars = 600;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_linkedin').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_linkedin').val().substring(maxChars, rest);
        jQuery('#preLinkedin').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownLink").html(remaining);
    });

    jQuery(document).on("keyup", '#prg_pinterest', function () {
        var maxChars = 500;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_pinterest').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_pinterest').val().substring(maxChars, rest);
        jQuery('#prePinterest').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownPint").html(remaining);
    });

    jQuery(document).on("keydown", '#prg_pinterest', function () {
        var maxChars = 500;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_pinterest').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_pinterest').val().substring(maxChars, rest);
        jQuery('#prePinterest').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownPint").html(remaining);
    });

    jQuery(document).on("keyup", '#prg_xing', function () {
        var xingOfficial = 420;
        if (jQuery("#urlForXing").val() != '') {
            xingOfficial = 419;
        }

        var maxChars = xingOfficial - jQuery("#urlForXing").val().length;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_xing').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_xing').val().substring(maxChars, rest);
        secondtext = secondtext + '</span>\n\
\n\
<span id="urlXing"><a id="aXing" href="' + jQuery("#urlForXing").val() + '">' + jQuery("#urlForXing").val() + '</a></span>';
        jQuery('#preXing').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownXing").html(remaining);
    });

    jQuery(document).on("keydown", '#prg_xing', function () {
        var xingOfficial = 420;
        if (jQuery("#urlForXing").val() != '') {
            xingOfficial = 419;
        }

        var maxChars = xingOfficial - jQuery("#urlForXing").val().length;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_xing').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_xing').val().substring(maxChars, rest);
        secondtext = secondtext + '</span>\n\
\n\
<span id="urlXing"><a id="aXing" href="' + jQuery("#urlForXing").val() + '">' + jQuery("#urlForXing").val() + '</a></span>';
        jQuery('#preXing').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownXing").html(remaining);
    });

    jQuery(document).on("keydown", '#prg_diigo', function () {
        var maxChars = 250;
        var remaining;

        var rest = parseInt(jQuery(this).val().length);
        var firsttext = jQuery('#prg_diigo').val().substring(0, maxChars);
        firsttext = firsttext + '<span class="my_span">';
        var secondtext = jQuery('#prg_diigo').val().substring(maxChars, rest);
        jQuery('#preDiigo').html(firsttext + secondtext);
        remaining = maxChars - jQuery(this).val().length;
        if (remaining <= 0) {
            remaining = 0;
        }
        jQuery("#countdownDiigo").html(remaining);
    });

    jQuery('#prg_twitter').trigger('keyup');
    jQuery('#prg_facebook').trigger('keyup');
    jQuery('#prg_linkedin').trigger('keyup');
    jQuery('#prg_pinterest').trigger('keyup');
    jQuery('#prg_xing').trigger('keyup');
    jQuery('#prg_diigo').trigger('keyup');

    jQuery(".snChecks").change(function () {
        var n = jQuery(".snChecks:checked").length;
        if (n === 0) {
            jQuery('#btnSubmitSocial').attr("disabled", "diabled");
        } else {
            jQuery('#btnSubmitSocial').removeAttr("disabled");
        }
    });

    jQuery("#checkFacebook").change(function () {
        if (jQuery("#checkFacebook:checked").length) {
            jQuery("#prg_facebook").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_facebook").css("color", "#555555");
            jQuery("#prg_facebook").attr("readonly", false);
        } else {
            jQuery("#prg_facebook").attr("readonly", true);
            jQuery("#prg_facebook").css("background-color", "#EBEBE4");
            jQuery("#prg_facebook").css("color", "#ACA899");
        }
    });

    jQuery("#checkTwitter").change(function () {
        if (jQuery("#checkTwitter:checked").length) {
            jQuery("#prg_twitter").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_twitter").css("color", "#555555");
            jQuery("#prg_twitter").attr("readonly", false);
            jQuery("#countdownTwit").show();
        } else {
            jQuery("#prg_twitter").attr("readonly", true);
            jQuery("#prg_twitter").css("background-color", "#EBEBE4");
            jQuery("#prg_twitter").css("color", "#ACA899");
            jQuery("#countdownTwit").hide();
        }
    });

    jQuery("#checkLinkedin").change(function () {
        if (jQuery("#checkLinkedin:checked").length) {
            jQuery("#prg_linkedin").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_linkedin").css("color", "#555555");
            jQuery("#prg_linkedin").attr("readonly", false);
            jQuery("#countdownLink").show();
        } else {
            jQuery("#prg_linkedin").attr("readonly", true);
            jQuery("#prg_linkedin").css("background-color", "#EBEBE4");
            jQuery("#prg_linkedin").css("color", "#ACA899");
            jQuery("#countdownLink").hide();
        }
    });

    jQuery("#checkTumblr").change(function () {
        if (jQuery("#checkTumblr:checked").length) {
            jQuery("#prg_tumblr").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_tumblr").css("color", "#555555");
            jQuery("#prg_tumblr").attr("readonly", false);
        } else {
            jQuery("#prg_tumblr").attr("readonly", true);
            jQuery("#prg_tumblr").css("background-color", "#EBEBE4");
            jQuery("#prg_tumblr").css("color", "#ACA899");
        }
    });

    jQuery("#checkStorify").change(function () {
        if (jQuery("#checkStorify:checked").length) {
            jQuery("#prg_storify").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_storify").css("color", "#555555");
            jQuery("#prg_storify").attr("readonly", false);
        } else {
            jQuery("#prg_storify").attr("readonly", true);
            jQuery("#prg_storify").css("background-color", "#EBEBE4");
            jQuery("#prg_storify").css("color", "#ACA899");
        }
    });

    jQuery("#checkPinterest").change(function () {
        if (jQuery("#checkPinterest:checked").length) {
            jQuery("#prg_pinterest").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_pinterest").css("color", "#555555");
            jQuery("#prg_pinterest").attr("readonly", false);
            jQuery("#countdownPint").show();
        } else {
            jQuery("#prg_pinterest").attr("readonly", true);
            jQuery("#prg_pinterest").css("background-color", "#EBEBE4");
            jQuery("#prg_pinterest").css("color", "#ACA899");
            jQuery("#countdownPint").hide();
        }
    });

    jQuery("#checkFlickr").change(function () {
        if (jQuery("#checkFlickr:checked").length) {
            jQuery("#prg_flickr").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_flickr").css("color", "#555555");
            jQuery("#prg_flickr").attr("readonly", false);
        } else {
            jQuery("#prg_flickr").attr("readonly", true);
            jQuery("#prg_flickr").css("background-color", "#EBEBE4");
            jQuery("#prg_flickr").css("color", "#ACA899");
        }
    });

    jQuery("#checkXing").change(function () {
        if (jQuery("#checkXing:checked").length) {
            jQuery("#prg_xing").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_xing").css("color", "#555555");
            jQuery("#prg_xing").attr("readonly", false);
            jQuery("#countdownXing").show();
        } else {
            jQuery("#prg_xing").attr("readonly", true);
            jQuery("#prg_xing").css("background-color", "#EBEBE4");
            jQuery("#prg_xing").css("color", "#ACA899");
            jQuery("#countdownXing").hide();
        }
    });

    jQuery("#checkDiigo").change(function () {
        if (jQuery("#checkDiigo:checked").length) {
            jQuery("#prg_diigo").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_diigo").css("color", "#555555");
            jQuery("#prg_diigo").attr("readonly", false);
        } else {
            jQuery("#prg_diigo").attr("readonly", true);
            jQuery("#prg_diigo").css("background-color", "#EBEBE4");
            jQuery("#prg_diigo").css("color", "#ACA899");
        }
    });

    jQuery("#checkGoogleplus").change(function () {
        if (jQuery("#checkGoogleplus:checked").length) {
            jQuery("#prg_googleplus").css("background-color", "rgba(0, 0, 0, 0)");
            jQuery("#prg_googleplus").css("color", "#555555");
            jQuery("#prg_googleplus").attr("readonly", false);
        } else {
            jQuery("#prg_googleplus").attr("readonly", true);
            jQuery("#prg_googleplus").css("background-color", "#EBEBE4");
            jQuery("#prg_googleplus").css("color", "#ACA899");
        }
    });

    jQuery("#btnPreviewFacebook").click(function () {
        jQuery('#btnSubmitSocial').attr("disabled", "diabled");
        var postData = {
            comment: jQuery("#prg_facebook").val(),
            username: jQuery("#facebookName").val(),
            imgSrc: jQuery("input:radio[name=image]:checked").val(),
            title: jQuery("#postTitle").val()
        };
        jQuery.ajax({
            type: "POST",
            cache: false,
            url: "admin.php?prgPluginExtern=facebookPreview",
            data: postData,
            success: function (data) {
                jQuery.fancybox({
                    content: data,
                    frameWidth: 500, // set the width
                    autoDimensions: false,
                    onClosed: function () {
                        jQuery('#btnSubmitSocial').removeAttr("disabled");
                    }
                });
            } // success
        });
    });

    jQuery("#prgConnect_sendSocial").validate({
        onfocusout: false,
        onkeyup: false,
        onclick: false,
        onsubmit: true,
        rules: {
            prg_twitter: {
                rangelength: function () {
                    if (document.prgConnect_sendSocial.checkTwitter.checked == true) {
                        console.log(1, 139 - (jQuery("#urlForTwit").val().length == 0 ? -1 : jQuery("#urlForTwit").val().length));
                        return [1, 139 - (jQuery("#urlForTwit").val().length == 0 ? -1 : jQuery("#urlForTwit").val().length)];
                    } else {
                        return [0, Infinity];
                    }
                }
            },
            prg_linkedin: {
                rangelength: function () {
                    if (document.prgConnect_sendSocial.checkLinkedin.checked == true) {
                        return [1, 600];
                    } else {
                        return [0, Infinity];
                    }
                }
            },
            prg_pinterest: {
                rangelength: function () {
                    if (document.prgConnect_sendSocial.checkPinterest.checked == true) {
                        return [1, 500];
                    } else {
                        return [0, Infinity];
                    }
                }
            },
            prg_xing: {
                rangelength: function () {
                    if (document.prgConnect_sendSocial.checkXing.checked == true) {
                        console.log(1, 419 - (jQuery("#urlForXing").val().length == 0 ? -1 : jQuery("#urlForXing").val().length));
                        return [1, 419 - (jQuery("#urlForXing").val().length == 0 ? -1 : jQuery("#urlForXing").val().length)];
                    } else {
                        return [0, Infinity];
                    }
                }
            },
            prg_diigo: {
                rangelength: function () {
                    if (document.prgConnect_sendSocial.checkDiigo.checked == true) {
                        return [1, 250];
                    } else {
                        return [0, Infinity];
                    }
                }
            }
        },
        messages: validateMessages,
        submitHandler: function (form) {
            jQuery(".hider").hide("slow");

            jQuery('#btnSubmitSocial').attr("disabled", "diabled");
            jQuery('.snChecks').hide();


            jQuery("#statusFacebookSucc").hide("fast");
            jQuery("#statusFacebookFail").hide("fast");
            jQuery("#statusFacebookLoading").hide("fast");
            jQuery('#sentToFacebook').val('0');

            jQuery("#statusTwitterSucc").hide("fast");
            jQuery("#statusTwitterFail").hide("fast");
            jQuery("#statusTwitterLoading").hide("fast");
            jQuery('#sentToTwitter').val('0');
            jQuery("#twitterStatusSpan").html("");

            jQuery("#statusLinkedinSucc").hide("fast");
            jQuery("#statusLinkedinFail").hide("fast");
            jQuery("#statusLinkedinLoading").hide("fast");
            jQuery('#sentToLinkedin').val('0');

            jQuery("#statusTumblrSucc").hide("fast");
            jQuery("#statusTumblrFail").hide("fast");
            jQuery("#statusTumblrLoading").hide("fast");
            jQuery('#sentToTumblr').val('0');

            jQuery("#statusStorifySucc").hide("fast");
            jQuery("#statusStorifyFail").hide("fast");
            jQuery("#statusStorifyLoading").hide("fast");
            jQuery('#sentToStorify').val('0');

            jQuery("#statusPinterestSucc").hide("fast");
            jQuery("#statusPinterestFail").hide("fast");
            jQuery("#statusPinterestLoading").hide("fast");
            jQuery('#sentToPinterest').val('0');

            jQuery("#statusFlickrSucc").hide("fast");
            jQuery("#statusFlickrFail").hide("fast");
            jQuery("#statusFlickrLoading").hide("fast");
            jQuery('#sentToFlickr').val('0');

            jQuery("#statusXingSucc").hide("fast");
            jQuery("#statusXingFail").hide("fast");
            jQuery("#statusXingLoading").hide("fast");
            jQuery('#sentToXing').val('0');

            jQuery("#statusDiigoSucc").hide("fast");
            jQuery("#statusDiigoFail").hide("fast");
            jQuery("#statusDiigoLoading").hide("fast");
            jQuery('#sentToDiigo').val('0');

            jQuery("#statusGoogleplusSucc").hide("fast");
            jQuery("#statusGoogleplusFail").hide("fast");
            jQuery("#statusGoogleplusLoading").hide("fast");
            jQuery('#sentToGoogleplus').val('0');

            if (document.prgConnect_sendSocial.checkFacebook.checked == true) {
                jQuery("#statusFacebookLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkTwitter.checked == true) {
                jQuery("#statusTwitterLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkLinkedin.checked == true) {
                jQuery("#statusLinkedinLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkTumblr.checked == true) {
                jQuery("#statusTumblrLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkStorify.checked == true) {
                jQuery("#statusStorifyLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkPinterest.checked == true) {
                jQuery("#statusPinterestLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkFlickr.checked == true) {
                jQuery("#statusFlickrLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkXing.checked == true) {
                jQuery("#statusXingLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkDiigo.checked == true) {
                jQuery("#statusDiigoLoading").show("fast");
            }
            if (document.prgConnect_sendSocial.checkGoogleplus.checked == true) {
                jQuery("#statusGoogleplusLoading").show("fast");
            }

            jQuery.ajax({
                processData: false,
                url: "/wp-admin/admin.php?prgPluginExtern=publishOnSN",
                type: "POST",
                dataType: "json",
                cache: false,
                data: jQuery(form).serialize(),
                success: function (data) {
                    jQuery(".spinner").hide("fast");
                    if (document.prgConnect_sendSocial.checkFacebook.checked === true) {
                        if (data[1] == true) {
                            jQuery("#statusFacebookSucc").show("fast");
                        } else {
                            jQuery("#statusFacebookFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkTwitter.checked === true) {
                        if (data[2] == true) {
                            jQuery("#statusTwitterSucc").show("fast");
                        } else {
                            jQuery("#statusTwitterFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkLinkedin.checked === true) {
                        if (data[3] == true) {
                            jQuery("#statusLinkedinSucc").show("fast");
                        } else {
                            jQuery("#statusLinkedinFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkTumblr.checked === true) {
                        if (data[4] == true) {
                            jQuery("#statusTumblrSucc").show("fast");
                        } else {
                            jQuery("#statusTumblrFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkStorify.checked === true) {
                        if (data[5] == true) {
                            jQuery("#statusStorifySucc").show("fast");
                        } else {
                            jQuery("#statusStorifyFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkPinterest.checked === true) {
                        if (data[6] == true) {
                            jQuery("#statusPinterestSucc").show("fast");
                        } else {
                            jQuery("#statusPinterestFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkFlickr.checked === true) {
                        if (data[7] == true) {
                            jQuery("#statusFlickrSucc").show("fast");
                        } else {
                            jQuery("#statusFlickrFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkXing.checked === true) {
                        if (data[8] == true) {
                            jQuery("#statusXingSucc").show("fast");
                        } else {
                            jQuery("#statusXingFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkDiigo.checked === true) {
                        if (data[9] == true) {
                            jQuery("#statusDiigoSucc").show("fast");
                        } else {
                            jQuery("#statusDiigoFail").show("fast");
                        }
                    }
                    if (document.prgConnect_sendSocial.checkGoogleplus.checked === true) {
                        if (data[10] == true) {
                            jQuery("#statusGoogleplusSucc").show("fast");
                        } else {
                            jQuery("#statusGoogleplusFail").show("fast");
                        }
                    }
                }
            });
        }
    });

    jQuery(".divOverUrl").width(jQuery("#urlTwit").width());

    jQuery(".divOverUrl").dblclick(function () {

        jQuery(".divOverUrl").html(jQuery("#urlTwit").text());
        jQuery(".divOverUrl").prop('contenteditable', true);
        jQuery("#aTwit").html(' ');
        jQuery(".divOverUrl").focus();
        jQuery(".divOverUrl").width(jQuery("#prg_twitter").width());
    });

    jQuery(".divOverUrl").keypress(function (e) {
        if (e.keyCode === 13) {
            jQuery(".divOverUrl").trigger('blur');
        }

    });

    jQuery(".divOverUrl").blur(function () {

        jQuery("#aTwit").html('');
        jQuery("#urlForTwit").val(jQuery(".divOverUrl").text());

        jQuery('#prg_twitter').trigger('keydown');
        jQuery(".divOverUrl").html('');
        jQuery(".divOverUrl").prop('contenteditable', false);

        var widthDivOverUrl = jQuery("#urlTwit").width();

        if (widthDivOverUrl < 10) {
            widthDivOverUrl = 120;
        }

        jQuery(".divOverUrl").width(widthDivOverUrl);
    });
    
    jQuery(".divOverUrlXing").width(jQuery("#urlXing").width());

    jQuery(".divOverUrlXing").dblclick(function () {

        jQuery(".divOverUrlXing").html(jQuery("#urlXing").text());
        jQuery(".divOverUrlXing").prop('contenteditable', true);
        jQuery("#aXing").html(' ');
        jQuery(".divOverUrlXing").focus();
        jQuery(".divOverUrlXing").width(jQuery("#prg_xing").width());
    });

    jQuery(".divOverUrlXing").keypress(function (e) {
        if (e.keyCode === 13) {
            jQuery(".divOverUrlXing").trigger('blur');
        }

    });

    jQuery(".divOverUrlXing").blur(function () {

        jQuery("#aXing").html('');
        jQuery("#urlForXing").val(jQuery(".divOverUrlXing").text());

        jQuery('#prg_xing').trigger('keydown');
        jQuery(".divOverUrlXing").html('');
        jQuery(".divOverUrlXing").prop('contenteditable', false);

        var widthDivOverUrl = jQuery("#urlXing").width();

        if (widthDivOverUrl < 10) {
            widthDivOverUrl = 120;
        }

        jQuery(".divOverUrlXing").width(widthDivOverUrl);
    });

    jQuery("#tumblrTagAddBtn").click(function () {
        var tumblrTags = jQuery(".inputTumblrTags").length;
        if (tumblrTags % 3 == 0) {
            jQuery("#tumblrDivTagAddBtn").before('</div><div class="col-xs-12 tagsTumblr">');
        }
        jQuery("#tumblrDivTagAddBtn").before('<div class="col-xs-3"><input class="form-control inputTumblrTags" name="prg_tumblr_tags[]"></div>');
        jQuery(".inputTumblrTags").last().focus();
    });

    jQuery("#diigoTagAddBtn").click(function () {
        var diigoTags = jQuery(".inputDiigoTags").length;
        if (diigoTags % 3 == 0) {
            jQuery("#diigoDivTagAddBtn").before('</div><div class="col-xs-12 tagsDiigo">');
        }
        jQuery("#diigoDivTagAddBtn").before('<div class="col-xs-3"><input class="form-control inputDiigoTags" name="prg_diigo_tags[]"></div>');
        jQuery(".inputDiigoTags").last().focus();
    });

});

function openInfoBox(text) {
    parent.jQuery.fancybox({
        'autoDimensions': true,
        'transitionIn': 'fade',
        'transitionOut': 'fade',
        'scrolling': 'auto',
        'content': text
    });
}