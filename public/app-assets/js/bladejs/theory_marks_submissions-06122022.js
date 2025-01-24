$(document).ready(function() { 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#" + "marks_submissions_id";
	
	$(formId).validate({
		/*
		rules: {
			address1: { required: true,minlength: 2,maxlength: 70},
		},
		messages: {
			address1: { 
				required: "Address1 is required", 
				minlength: "Address1 must be of 2 charector",
				maxlength: "Address1 cannot be more than 70 charector",
			},
		},
		*/
		success: function(response){
			
		},
		submitHandler: function(form) {
			
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxMarkSubmmisionsValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response.isValid==false){ 
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
							var clsName = "api";
							if($(formId).hasClass(clsName)){
								return true;
							}
							swal({
								title: 'Are you sure want yo save your information.',
								text: "You won't be able to revert this!",
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







