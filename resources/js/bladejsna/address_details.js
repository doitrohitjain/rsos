// <script type="text/javascript">
	// jQuery Form Validator
	
	$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId; 
		var action = "checkAddressValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
				
		$(formId).validate({
			rules: {
				address1: { required: true,minlength: 2,maxlength: 70},
				address2: { required: true,minlength: 2,maxlength: 70},
				address3: { required: true,minlength: 2,maxlength: 70},
				state_id: "required",
				district_id: "required",
				tehsil_id: "required",
				city_name: { required: true,minlength: 2,maxlength: 30},
				pincode: { required: true,minlength: 6,maxlength: 8,number: true},
			},
			messages: {
				address1: { 
					required: "Address1 is required", 
					minlength: "Address1 must be of 2 charector",
					maxlength: "Address1 cannot be more than 70 charector",
				},
				address2: { 
					required: "Address2 is required", 
					minlength: "Address2 must be of 2 charector",
					maxlength: "Address2 cannot be more than 70 charector",
				},
				address3: { 
					required: "Address3 is required", 
					minlength: "Address3 must be of 2 charector",
					maxlength: "Address3 cannot be more than 70 charector",
				},
				state_id: "Please select the State",
				district_id: "Please select the District",
				tehsil_id: "Please select the Tehsil",
				city_name: { 
					required: "City Name is required", 
					minlength: "City Name must be of 2 charector",
					maxlength: "City Name cannot be more than 30 charector",
				},
				pincode: { 
					required: "Pincode is required", 
					minlength: "Pincode must be of 6 digits",
					maxlength: "Pincode cannot be more than 8 digits",
					number: "Pincode must be numeric"
				} 
			},
			success: function(response){
				
			},
			submitHandler: function(form) {
				if($('#ajaxRequest').val()==1){
				showLoading();
					$.ajax({
						url: config.routes.checkAddressValidation,
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
		});
	});
	// jQuery Form Validator
    
	$('body').on('change', '.state_id', function (){ 
		$('.district_id').html('<option value="">Select district</option>');
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		var id = $(this).val();
		if(id!=''){ 
			showLoading();
			// initialize
			$('.district_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.district_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
		
			event.preventDefault();
			$.ajax({
				url: config.routes.district_by_state_id,
				type: "get",
				data: {'id': id},
				success: function (data) { 
					$.each(data, function(key,value) {
						$('.district_id').append('<option value="' + key + '">' + value + '</option>');
						$(".district_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	
	$('body').on('change', '.district_id', function (){
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		var id = $(this).val();
		if(id!=''){
			showLoading();
			// initialize
			$('.tehsil_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.tehsil_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
		
			event.preventDefault();
			$.ajax({
				// url: "{{ route('get_tehsil_by_district_id') }}",
				url: config.routes.get_tehsil_by_district_id,
				type: "get",
				data: {'id': id},
				success: function (data) { 
					$.each(data, function(key,value) {
						$('.tehsil_id').append('<option value="' + key + '">' + value + '</option>');
						$(".tehsil_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
// </script>



