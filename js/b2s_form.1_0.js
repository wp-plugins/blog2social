jQuery(document).ready(function() {

    if (jQuery("#lang").val() == "de") {
        var validateMessages = {
            title: "Bitte Titel angeben!",
            message: "Bitte Text verfassen!",
            kategorie_id: "Bitte Kategorie angeben!",
            name_presse: "Bitte Firma angeben!",
            anrede_presse: "Bitte Anrede auswählen!",
            vorname_presse: "Bitte Vornamen angeben!",
            nachname_presse: "Bitte Nachnamen angeben!",
            strasse_presse: "Bitte Straße angeben!",
            nummer_presse: "Bitte Hausnummer angeben!",
            plz_presse: "Bitte Postleitzahl angeben!",
            ort_presse: "Bitte Stadt angeben!",
            land_presse: "Bitte Land auswählen!",
            telefon_presse: "Bitte Telefonnummer angeben!",
            email_presse: {
                required: "Bitte eMail angeben!",
                email: "Bitte korrekte Mailadresse angeben!"
            },
            url_presse: {
                required: "Bitte Website angeben!",
                url: "Bitte korrekte Website angeben!"
            },
            name_mandant: "Bitte Namen angeben!",
            anrede_mandant: "Bitte Anrede auswählen!",
            vorname_mandant: "Bitte Vornamen angeben!",
            nachname_mandant: "Bitte Nachnamen angeben!",
            strasse_mandant: "Bitte Straße angeben!",
            nummer_mandant: "Bitter Hausnummer angeben!",
            plz_mandant: "Bitte Postleitzahl angeben!",
            ort_mandant: "Bitte Stadt angeben!",
            land_mandant: "Bitte Land auswählen!",
            telefon_mandant: "Bitte Telefonnummer angeben!",
            email_mandant: {
                required: "Bitte eMail angeben!",
                email: "Bitte korrekte Mailadresse angeben!"
            },
            url_mandant: {
                required: "Bitte Website angeben!",
                url: "Bitte korrekte Website angeben!"
            },
            info_mandant: {
                required: "Bitte Firmenbeschreibung angeben!",
                minlength: "Mindestens 20 Zeichen erforderlich!"
            },
            bildtitel: "Bitte Namen angeben",
            bildcopyright: "Bitte Urheber angeben",
            bestaetigung: "Bitte beachten Sie, dass gegebenenfalls Gebühren anfallen können.\n\rEinmal versandte Pressemitteilungen können nicht mehr zurückgenommen werden. Soll Ihre Pressemitteilung jetzt versendet werden?",
            fehler_bild: "Fehler beim Versand, bitte Bildformat überprüfen",
            fehler: "Fehler beim Speichern",
            erfolg: "Mitteilung erfolgreich übermittelt!"
        };
    } else if (jQuery("#lang").val() == "en") {
        var validateMessages = {
            title: "Please set Title",
            message: "Please set Text",
            kategorie_id: "Please set Category",
            name_presse: "Please set Company",
            anrede_presse: "Please set a salutation",
            vorname_presse: "Please set first Name",
            nachname_presse: "Please set last Name",
            strasse_presse: "Please set a Street",
            nummer_presse: "Please set a Number",
            plz_presse: "Please set a Zip",
            ort_presse: "Please set a City",
            land_presse: "Please set a country",
            telefon_presse: "Please set phone number",
            email_presse: {
                required: "Please set E-Mail",
                email: "Please set correct E-Mail"
            },
            url_presse: {
                required: "Please set Website",
                url: "Please set correct Website!"
            },
            name_mandant: "Please set Name",
            anrede_mandant: "Please set a salutation",
            vorname_mandant: "Please set a first Name",
            nachname_mandant: "Please set a last Name",
            strasse_mandant: "Please set a Street",
            nummer_mandant: "Please set a number",
            plz_mandant: "Please set a Zip",
            ort_mandant: "Please set a City",
            land_mandant: "Please set a Country",
            telefon_mandant: "Please set a phonenumber",
            email_mandant: {
                required: "Please set E-Mail",
                email: "Please set correct E-Mail"
            },
            url_mandant: {
                required: "Please set Website",
                url: "Please set correct Website!"
            },
            info_mandant: {
                required: "Please set Company Description",
                minlength: "At least 20 characters"
            },
            bildtitel: "Please set Name",
            bildcopyright: "Please set Owner",
            bestaetigung: "Please note that, you may be charged.\n\rOnce sent press releases may not be withdrawn. Do you intend to send the message?",
            fehler_bild: "Error with the transfer, please check the format of the image",
            fehler: "Message transfer failed",
            erfolg: "Message successful transfered"
        };
    }

    jQuery("#btnChangeParticulars").click(function() {
        saved.style.display = 'block';
        btnChangeParticulars.style.display = 'none';
    });

    jQuery("#img0").change(function() {
        img_rights.style.display = 'none';
    });

    jQuery(".newImg").change(function() {
        img_rights.style.display = 'block';
    });
    
    if(jQuery(".newImg")[0] != undefined && jQuery(".newImg")[0].checked) {
        img_rights.style.display = 'block';
    }

    jQuery("#prgConnect_sendPress").validate({
        ignore: "",
        onfocusout: false,
        onkeyup: false,
        onclick: false,
        onsubmit: true,
        invalidHandler: function(e, validator) {
            if (validator.errorList.length) {
                jQuery('a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
            }
        },
        rules: {
            title: "required",
            message: "required",
            kategorie_id: "required",
            name_presse: "required",
            anrede_presse: "required",
            vorname_presse: "required",
            nachname_presse: "required",
            strasse_presse: "required",
            nummer_presse: "required",
            plz_presse: "required",
            ort_presse: "required",
            land_presse: "required",
            telefon_presse: "required",
            email_presse: {
                required: true,
                email: true
            },
            url_presse: {
                required: true,
                url: true
            },
            name_mandant: "required",
            anrede_mandant: "required",
            vorname_mandant: "required",
            nachname_mandant: "required",
            strasse_mandant: "required",
            nummer_mandant: "required",
            plz_mandant: "required",
            ort_mandant: "required",
            land_mandant: "required",
            telefon_mandant: "required",
            email_mandant: {
                required: true,
                email: true
            },
            url_mandant: {
                required: true,
                url: true
            },
            info_mandant: {
                required: true,
                minlength: 20
            },
            bildtitel: {
                required: ".newImg:checked"
            },
            bildcopyright: {
                required: ".newImg:checked"
            }
        },
        messages: validateMessages,
        submitHandler: function(form) {
            if (form.publish[0] != undefined) {
                ret = window.confirm(validateMessages.bestaetigung);
                if (ret === false) {
                    return false;
                }
            }

            jQuery.ajax({
                url: "admin.php?prgPluginExtern=sendMessage",
                type: "POST",
                dataType: "json",
                cache: false,
                data: jQuery(form).serialize(),
                success: function(data) {
                    if(data.success == "true") {
                        parent.jQuery.fancybox(validateMessages.erfolg);
                    } else {
                        parent.jQuery.fancybox(validateMessages.fehler);
                    }
                }
            });
        }
    });
});
