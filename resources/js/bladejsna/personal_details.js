// <script type="text/javascript">
	 $(function() {
		$( "#my_date_picker").datepicker({
			maxDate: new Date(),
			minDate : new Date('2006-3-28'),
			dateFormat : "dd-mm-yy",
		});
	});
	// jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + config.data.formId; 
		$(formId).validate({
			rules: {
				 name: { required: true,minlength: 2,maxlength: 100},
				 father_name: { required: true,minlength: 2,maxlength: 100},
				 mother_name: { required: true,minlength: 2,maxlength: 100},
				 aadhar_number: { required: true,digits:true, minlength:12,maxlength:12},
				 mobile: { required: true,digits:true, minlength:10,maxlength:10},
				 adm_type:"required",
				 course: "required",
				 gender_id:"required",
				 nationality: "required",
				 religion_id: "required",
				 category_a:"required",
				 disability:"required",
				 disadvantage_group: "required",
				 medium: "required",
				 rural_urban:"required",
				 employment: "required",
				 pre_qualification: "required",
				 year_pass:"required",
				 dob: "required",
			},
			errorElement: 'div', 
			messages: {
				name: { 
					required: "Name is required", 
					minlength: "Name must be of 2 charector",
					maxlength: "Name cannot be more than 100 charector",
				},
				father_name: { 
					required: "Father Name is required", 
					minlength: "Father Name must be of 2 charector",
					maxlength: "Father Name cannot be more than 70 charector",
				},
				mother_name: { 
					required: "Mother Name is required", 
					minlength: "Mother Name must be of 2 charector",
					maxlength: "Mother Name cannot be more than 70 charector",
				},
				mobile: {
				required: "Please enter contact number",
				minlength: "The contact number should be 10 digits",
				digits: "Please enter only numbers",
				maxlength: "The contact number should be 10 digits",
				},
				aadhar_number: {
				required: "Please enter Aadhar Card number",
				minlength: "The Aadhar Card should be 12 digits",
				digits: "Please enter only numbers",
				maxlength: "The Aadhar Card should be 12 digits",
				},
				gender_id: "Please select the Gender Type",
				nationality: "Please select the Nationality Type",
				religion_id: "Please select the Religion Type",
				category_a: "Please select the Category Type",
				disability: "Please select the Disability Type",
				disadvantage_group: "Please select the Disadvantage Group Type",
				medium: "Please select the Medium Type",
				rural_urban: "Please select the Rural Urban Type",
				employment: "Please select the Employment Type",
				pre_qualification: "Please select the Pre Qualification  Type",
				year_pass: "Please select the Year Pass Type",
				
			},
			success: function(response){
			},
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



