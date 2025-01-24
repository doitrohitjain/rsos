$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#studentMultiEnrollmentForm";
	var action = "set_student_multi_enrollment";
	var ajaxUrl = ajaxBaseUrl + action; 
	var selectItemId = "#student_multi_enrollment";
	  var route = window.location.origin+"/rsos/student/studentsdashboards";
	// jQuery Form Validator 
	$(".student_multi_enrollment").on("select2:select", function(event) {
		var selectedVal = $(event.currentTarget).find("option:selected").val();
		if(selectedVal != ''){ 
			showLoading();
			// initialize 
			event.preventDefault();
			$.ajax({
				url: config.routes.set_student_multi_enrollment,
				type: "get",
				data: {"selectedVal":selectedVal},
				success: function (response) { 
					hideLoading();
					 window.location = route;
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});

});