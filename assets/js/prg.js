jQuery(document).ready(function(){jQuery(".checkPRGButton").removeAttr("disabled");jQuery("#loginPRGButton").removeAttr("disabled");jQuery("#checkJs").val("1");jQuery("#btnChangeParticulars").click(function(){saved.style.display="block";btnChangeParticulars.style.display="none"});jQuery("#img0").change(function(){img_rights.style.display="none"});jQuery(".newImg").change(function(){img_rights.style.display="block"});if(jQuery(".newImg")[0]!=undefined&&jQuery(".newImg")[0].checked)img_rights.style.display=
"block";jQuery("#loginPRGButton").click(function(){var url=jQuery("#plugin_url").val();var lang=jQuery("#lang").val();var postId=jQuery("#postId").val();jQuery("#loginPRG").validate({ignore:"",rules:{username:{required:true},password:{required:true}},errorPlacement:function(){return false},submitHandler:function(form){jQuery("#b2sLoader").show();jQuery(".loginPRG").hide();jQuery(".loginPRGWarning").hide();jQuery(".loginPRGDanger").hide();jQuery.ajax({url:url+"/Call.php",type:"POST",dataType:"json",
cache:false,data:jQuery(form).serialize(),success:function(data){var result=unescape(data["result"]);if(result=="0"){jQuery("#b2sLoader").hide();jQuery(".loginPRG").show();jQuery(".loginPRGWarning").show();jQuery(".loginPRGDanger").hide()}if(result=="2"){jQuery("#b2sLoader").hide();jQuery(".loginPRG").show();jQuery(".loginPRGWarning").hide();jQuery(".loginPRGDanger").show()}if(result=="1")window.location.href=window.location.pathname+"?page=formPRG&postId="+postId+"&lang="+lang;return false}})}})});
jQuery("#logoutPRG").click(function(){var url=jQuery("#plugin_url").val();jQuery(".sentMessagePRG").hide();jQuery("#b2sLoader").show();jQuery.ajax({cache:false,type:"POST",url:url+"/Call.php",dataType:"json",data:{token:jQuery("#token").val(),action:"logoutPRG"},success:function(data){var result=unescape(data["result"]);if(result=="1"){parent.window.location.href=parent.window.location.pathname+"?page=blog2social&logout=true";return false}}})});jQuery("#sentMessagePRG").validate({ignore:"",onsubmit:true,
invalidHandler:function(e,validator){if(validator.errorList.length)jQuery('a[href="#'+jQuery(validator.errorList[0].element).closest(".tab-pane").attr("id")+'"]').tab("show")},rules:{title:"required",message:"required",kategorie_id:"required",name_presse:"required",anrede_presse:"required",vorname_presse:"required",nachname_presse:"required",strasse_presse:"required",nummer_presse:"required",plz_presse:"required",ort_presse:"required",land_presse:"required",telefon_presse:"required",email_presse:{required:true,
email:true},url_presse:{required:true,url:true},name_mandant:"required",anrede_mandant:"required",vorname_mandant:"required",nachname_mandant:"required",strasse_mandant:"required",nummer_mandant:"required",plz_mandant:"required",ort_mandant:"required",land_mandant:"required",telefon_mandant:"required",email_mandant:{required:true,email:true},url_mandant:{required:true,url:true},info_mandant:{required:true,minlength:20},bildtitel:{required:".newImg:checked"},bildcopyright:{required:".newImg:checked"}},
errorPlacement:function(error,element){return true},submitHandler:function(form){var url=jQuery("#plugin_url").val();var blog_user_id=jQuery("#blog_user_id").val();var post_id=jQuery("#post_id").val();if(form.publish[0]!=undefined){var lang=jQuery("#lang").val();var confirm="Bitte beachten Sie, dass gegebenenfalls Geb\u00fchren f\u00fcr die Versendung von Pressemitteilungen \u00fcber PR-Gateway anfallen k\u00f6nnen.\n\rEinmal versandte Pressemitteilungen k\u00f6nnen nicht mehr zur\u00fcckgenommen werden. \n\r\n\rSoll Ihre Pressemitteilung jetzt versendet werden?";
var confirm_en="Please note that, where appropriate, charges may apply.\n\rOnce sent press releases can not be taken back. \n\r\n\rYour press release should now be sent?";if(lang=="en")confirm=confirm_en;ret=window.confirm(confirm);if(ret===false)return false}jQuery("#b2sLoader").show();jQuery(".sentMessagePRG").hide();jQuery.ajax({url:window.location.pathname+"?page=blog2social&b2sConnect=sentPRG&blog_user_id="+blog_user_id+"&post_id="+post_id,type:"POST",dataType:"json",cache:false,data:jQuery(form).serialize(),
success:function(data){var result=unescape(data["result"]);var create=unescape(data["create"]);if(result=="1"&&create=="1"){jQuery("#b2sLoader").hide();jQuery(".sentMessagePRG").hide();jQuery(".sentMessagePRGSuccess").show()}else{jQuery("#b2sLoader").hide();jQuery(".sentMessagePRG").hide();jQuery(".sentMessagePRGWarning").show()}}})}})});