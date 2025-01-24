$(function(){
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "reloadcaptcha";
	var ajaxUrl = ajaxBaseUrl + action;
	$('#reload').click(function () {
        $.ajax({
            type: 'GET',
			url: config.routes.reloadcaptcha,
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
});




