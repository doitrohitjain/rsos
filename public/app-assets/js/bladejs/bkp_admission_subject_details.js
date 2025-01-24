// <script type="text/javascript">
	// jQuery Form Validator
	
	$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
	
		var action = "ajaxSubjectValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
				
		$(formId).validate({
			success: function(response){
				
			},
			submitHandler: function(form) { 
				if($('#ajaxRequest').val()==1){
				showLoading();
					$.ajax({
						url: config.routes.ajaxSubjectValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response != 1){
								var message = "";
								$.each(response, function() {
									$.each(this, function(k, v) {
										message += v;
									});
								});
								swal({
									title: "Validation Error",
									text: message,
									icon: "error",
									button: "Close",
									timer: 30000
								});
								return false;
							} else {
							$('#ajaxRequest').val('0');
							   $(formId).submit();
								
							}
						
						error: function (data) {
							console.log('Error:', data); 
							hideLoading();
						}
					});
				} else {
					return true;
				} 
			}
			}
		});
	}); 
}); 

// </script>



