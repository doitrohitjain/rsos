$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		var action = "ajaxRevalSubjectValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
				
		$(formId).validate({
			success: function(response){
				
			},
			submitHandler: function(form) { 
				//return true;
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.ajaxRevalSubjectValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response.isValid==false){
								var message = response.error;
								swal({
									title: "Validation Error",
									text: message,
									icon: "error",
									button: "Close",
									timer: 30000
								});
								return false;
							} else {
								var clsName = "api";

								if($(formId).hasClass(clsName)){

								return true;
								}
								swal({
									title: 'Are you sure save your information.',
									//text: "You won't be able to revert this!",
									//text: form_edit_msg,
									icon: 'success',
									buttons: true,
								 })
								.then((willsave) => {
									if (willsave) {
									
										$(formId).addClass(clsName); 
										$('#ajaxRequest').val('0');
										$(formId).submit();
									}
								});
							} 
						},
						error: function (data) {
							console.log('Error:', data); 
							hideLoading();
						}
					});
				} else {
					return true;
				} 
				
			}
		});
	});
	
	
	
	
	
	
	
	
	
	
	
	
	




