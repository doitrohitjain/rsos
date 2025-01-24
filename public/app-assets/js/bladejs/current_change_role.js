$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#roleChangeForm";
	var action = "setCurrentRole";
	var ajaxUrl = ajaxBaseUrl + action; 
	var selectItemId = "#current_change_role";

	// jQuery Form Validator 
	$(".current_change_role").on("select2:select", function(event) {
		var selectedVal = $(event.currentTarget).find("option:selected").val();
		 
		if(selectedVal != ''){ 
			showLoading();
			// initialize 
			event.preventDefault();
			$.ajax({
				url: config.routes.set_current_role,
				type: "get",
				data: {"selectedVal":selectedVal},
				success: function (data) { 
					hideLoading();
					// alert(data);
					// return false;
					
					$(location).prop('href', data);
					
					 
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});

});