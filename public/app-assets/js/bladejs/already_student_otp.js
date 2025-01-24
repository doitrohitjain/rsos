$(document).ready(function() {

	var div_timer;
	var myTimer;
	begin();
	function begin() {
		div_timer = 120;
		var link = $('.disabledCustom').attr('data-link');
		$('#div_timer').html(div_timer);
		myTimer = setInterval(function() {
		--div_timer;
		$('#div_timer').html(div_timer);
		if (div_timer === 0) {
			clearInterval(myTimer);
			$('#div_timer').html('');
			$('.disabledCustom').attr('href', link);
		}
		}, 1000);
	}
	
	
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxchecktopdetail";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxchecktopdetail,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response == false){
							swal({
								title: 'Invalid OTP.Please enter valid OTP',
								icon: 'error',
								showConfirmButton: false,
								timer: 10000
							});
							return false;
						   }else if(response == null){
							swal({
								title: 'Please enter OTP which received on your registered mobile number.',
								icon: 'error',
								showConfirmButton: false,
								timer: 10000
							});
							return false;
						} 
						    else if(response == true) {
							var clsName = "api";
							if($(formId).hasClass(clsName)){ 
								return true;
							}
							swal({
								title: 'OTP matched.',
								text: "Your OTP has been successfully matched and your SSO has been mapped successfully.",
								icon: 'success',
								showConfirmButton: true,
								timer: 10000
							 }).then(() => {
								$(formId).addClass(clsName); 
								$('#ajaxRequest').val('0');
								$(formId).submit();
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
 