// <script type="text/javascript">
	// jQuery Form Validator
	 $(document).ready(function() { 
		formId = "#" + config.data.formId; 
		$(formId).validate({
			rules: {
				NICCode:"required",
				//ecenter10:"required",
				//ecenter12:"required",
				//capacity:{ required: true,digits:true, minlength:2,maxlength:10},
				//cent_name:{ required: true,minlength: 2,maxlength: 200},
				//std_code: { required: true,digits:true, minlength:2,maxlength:10},
				//phone_res: { required: true,digits:true, minlength:2,maxlength:10},
				//center_supdt: "required",
				//mobile_centsupdt: { required: true,digits:true, minlength:10,maxlength:10},
				//exam_incharge: "required",
				//mobile: { required: true,digits:true, minlength:10,maxlength:10},
				//email : {required : true, maxlength : 100, email : true},
				//cent_add1: "required",
				//cent_add2:"required",
				//district_id: "required",
				//pin:{required: true,digits:true, minlength:6,maxlength:6},
				//police_station:"required",
				//accountno:{required: true,digits:true, minlength:2,maxlength:25},
				//bank_name: "required",
				//bank_ifsc: "required",
			},
			errorElement: 'div', 
			messages: {
				
				//mobile: {
				//required: "Please enter contact number",
				//minlength: "The contact number should be 10 digits",
				//digits: "Please enter only numbers",
				//maxlength: "The contact number should be 10 digits",
				//},
				//mobile_centsupdt: {
				//required: "Please enter contact number",
				//minlength: "The contact number should be 10 digits",
				//digits: "Please enter only numbers",
				//maxlength: "The contact number should be 10 digits",
				//},
				//email : {
				//required : 'Enter Email Detail',
				//email : 'Enter Valid Email Detail',
				//maxlength : 'Email should not be more than 50 character'
				//},
				NICCode: "Please Enter the NICCode",
				//ecenter10: "Please Enter the exam center 10th",
				//ecenter12: "Please Enter the exam center 12th",
				//capacity: "Please Enter the Capacity",
				//cent_name: "Please Enter the Center name",
				//std_code: "Please Enter the std code",
				//phone_res: "Please Enter the phone res",
				//center_supdt: "Please Enter the center supdt",
				//exam_incharge: "Please Enter the exam incharge",
				//cent_add1: "Please Enter the center address 1",
				//cent_add2: "Please Enter the center address 2",
				//district_id: "Please select the district Type",
				//pin: "Please Enter the pin code",
				//police_station: "Please Enter the Year Pass Type",
				//accountno: "Please Enter the account number",
				//bank_name: "Please Enter the bank name",
				//bank_ifsc: "Please Enter the bank ifsc",
				
			},
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



