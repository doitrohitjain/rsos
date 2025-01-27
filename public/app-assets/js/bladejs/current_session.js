$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#sessionForm";
	var action = "setCurrentSession";
	var ajaxUrl = ajaxBaseUrl + action; 
	var selectItemId = "#current_session";

	// jQuery Form Validator 
	$(".current_session").on("select2:select", function(event) {
		var selectedVal = $(event.currentTarget).find("option:selected").val();
		if(selectedVal != ''){ 
			showLoading();
			// initialize 
			event.preventDefault();
			$.ajax({
				
				url: config.routes.set_current_session,
				type: "get",
				data: {"selectedVal":selectedVal},
				success: function (data) { 
					hideLoading();
					window.location.reload();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});

});