    // jQuery Form Validator
	 $(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		var formId = "#result";
		var action = "checkresultstudent";
		var ajaxUrl = ajaxBaseUrl + action;
				
		$(formId).validate({
			success: function(response){
				
			},
			submitHandler: function(form) { 
				//return true;
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.checkresultstudent,
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
							var clsName = "api";

							if($(formId).hasClass(clsName)){

							return true;
							}
							swal({
								title: 'Are you sure you want to see result.',
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
	
// </script>
$(function() { 
$('#my_date_picker').datepicker({
		maxDate: new Date()
		});
  });