// <script type="text/javascript">
// jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + config.data.formId; 
		$(formId).validate({
	       success: function(response){
			},
			submitHandler: function(form) {
			  var clsName = "api";
				if($(formId).hasClass(clsName)){
						
						return true;
				}else{
					event.preventDefault();
					swal({
						title: 'Are you sure save your information.',
						text: "You won't be able to revert this!",
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
			//submitHandler: function(form) {
				//var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
				//var formId = "#" + config.data.formId; 
				//var action = "checkPersoanldetailValidation";
				//var ajaxUrl = ajaxBaseUrl + action;
				//$.ajax({
					//url: ajaxUrl,
					//type: "POST",
					//data: $(formId).serialize(),
					//success: function (response) { 
						//hideLoading();
						//if(response[0]['status'] == false){
							//var errors = response[0]['error'];
							 //alert(response[0]['error'].ssoid);
							//$("#validation-errors-div").removeClass("hide");
							//$('#validation-errors').append('<i class="material-icons"></i> ');
							//$.each(errors, function(key,value) { 
								 //$('#validation-errors').append('<div class="alert alert-danger">'+value+'</div');
							//}); 
						//}else {
							//$(formId).submit();
						//} 
					//},
					//error: function (data) {
						//toastr.error(data.success);
						//console.log('Error:', data); 
						//hideLoading();
					//}
				//});
				
			//}
		});
	});
	
// </script>



