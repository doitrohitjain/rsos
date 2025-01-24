// <script type="text/javascript">
	// jQuery Form Validator
	 
	
	
	$(document).ready(function() {
		 formId = "#" + config.data.formId; 
		$(formId).validate({
			rules: {
				 jan_aadhar_nubmer: { required: true,minlength: 2,maxlength: 70},
				 adm_type:"required",
				 course: "required",
				 stream: "required",
			},
			errorElement: 'div', 
			messages: {
				jan_aadhar_nubmer: { 
					required: "jan_aadhar_nubmer is required", 
					minlength: "jan_aadhar_nubmer must be of 2 charector",
					maxlength: "jan_aadhar_nubmer cannot be more than 70 charector",
				},
				adm_type: "Please select the Admission Type",
				course: "Please select the Course Type",
				stream: "Please select the Stream Type",
				
			},
			success: function(response){
				
			},
			//submitHandler: function(form) {
				//var formId = "#" + config.data.formId; 
				//var action = "checkRegistration";
				//var ajaxUrl = ajaxBaseUrl + action;
				//$.ajax({
					//url: ajaxUrl,
					//type: "POST",
					//data: $(formId).serialize(),
					//success: function (response) { 
						//hideLoading();
						//if(response[0]['status'] == false){
							//var errors = response[0]['error'];
							// alert(response[0]['error'].ssoid);
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



