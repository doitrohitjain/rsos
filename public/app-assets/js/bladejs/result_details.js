  // jQuery Form Validator
	 $(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		var formId = "#result";
		var action = "checkresultstudent";
		var ajaxUrl = ajaxBaseUrl + action;
				
		$(formId).validate({
			rules: {
				enrollment: { required: true,nowhitespace: true,digits:true,minlength: 11 ,maxlength:15 },
				dob: "required",
				captcha: "required",
			}, 
			messages: {
				enrollment: { 
					required: "Please enter enrollment number", 
					minlength: "The enrollment number should be 11 digits",
				    digits: "Please enter only numbers",
				    maxlength: "The enrollment number should be 11 digits",
				},
				dob: "Please Enter DOB.",
				captcha: "Please Enter Captcha.",
				
			},
			success: function(response){
				
			},
			submitHandler: function(form) { 
				//return true;
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.checkresultstudent,
						type: "POST",
						data: $(formId).serialize(),
						success: function (data) {
						if(data == false){
							swal({
								title: 'Result not found as per given details.',
								icon: 'error',
								button: "Close",
							});
						} else if(data == 'captchaFalse'){
							swal({
								title: 'Please enter valid captcha value.',
								icon: 'error',
								button: "Close",
								});
							$('#captcha').val('');
						}else {
							var clsName = "api";

							if($(formId).hasClass(clsName)){

							return true;
							}
							swal({
								title: 'Are you sure you want to see result.',
								text: "Please confirm",
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
	
// </script>
$(function() { 
	var maxBirthdayDate = new Date();
	maxBirthdayDate.setFullYear( maxBirthdayDate.getFullYear() - 12);
	var yearMaxBirthdayDate = new Date();
	yearMaxBirthdayDate.setFullYear( yearMaxBirthdayDate.getFullYear() - 25);
	
	$('#my_date_picker').datepicker({
		maxDate: maxBirthdayDate,
		defaultDate: yearMaxBirthdayDate
	});
	
	$("#captchaRefresh").click(function(){
		showLoading();
		$.ajax({
			url: config.routes.ajaxGenerateCaptcha,
			type: "GET",
			success: function (data) {
				$('#captchaImg').html(data);
				hideLoading();
			},
			error: function (data) {
				console.log('Error:', data); 
				hideLoading();
			}
		});
		hideLoading();
	});
	
});