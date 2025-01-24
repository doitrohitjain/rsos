
	$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		var action = "checkAddressValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
				
		$(formId).validate({
			rules: {
				enrollment: { required: true,minlength: 11,maxlength: 11,number: true},
			},
			messages: {
				subject_id: { 
					required: "Enrollment is required", 
					minlength: "Please enter valid enrollment number.",
					maxlength: "Please enter valid enrollment number.",
					number: "Enrollment must be numeric"
				} 
			},
			success: function(response){
				
			},
			submitHandler: function(form) {
				var clsName = "api";
				$('.subject_list').prop("disabled", false);
		
				if($(formId).hasClass(clsName)){
					return true;
				} else {
					event.preventDefault();
					swal({
						title: 'क्या आप वाकई दर्ज नामांकन संख्या खोजना चाहते हैं?(Are you sure you want to find entered enrollment number?)',
						text: "You won't be able to revert this?",
						icon: 'success',
						buttons: true,
					})
					.then((willsave) => {
						if (willsave) {
							$(formId).addClass(clsName); 
							$(formId).submit();
						}
					});
				}
			}
			/*
			submitHandler: function(form) {
				alert($('#ajaxRequest').val());
				if($('#ajaxRequest').val()==1){
				showLoading();
					$.ajax({
						url: config.routes.ajaxSuppFindEnrollmentValidation,
						type: "POST",
						data: $(formId).serialize(),
						success: function (response) { 
							if(response[0]['status'] == false){ 
								hideLoading();
								var errors = response[0]['error'];
								$("#validation-errors-div").removeClass("hide");
								$('#validation-errors').append('<i class="material-icons"></i> ');
								$.each(errors, function(key,value) { 
									 $('#validation-errors').append('<div class="alert alert-danger">'+value+'</div');
								});
							} else {
								$('#ajaxRequest').val('0');
								$(formId).submit();
								
								//$(formId).attr('id',config.data.formId +'_ajax_sucess');
								//setTimeout(function(){ $('#'+ config.data.formId +'_ajax_sucess').submit(); }, 3000);
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
			*/
		});
	}); 
	
	
	   