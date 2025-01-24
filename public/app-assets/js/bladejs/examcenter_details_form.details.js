// <script type="text/javascript">
	// jQuery Form Validator
	 $(document).ready(function() { 
		formId = "#" + config.data.formId; 
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		var action = "ajaxCheckSsoAlreadyExamCenter";
		var ajaxUrl = ajaxBaseUrl + action; 
		
		$(formId).validate({
			rules2: {
				ecenter10:{ required: true,digits:true, minlength:2,maxlength:10},
				ecenter12:{required: true,digits:true, minlength:2,maxlength:10},
				capacity:{ required: true,digits:true, minlength:2,maxlength:10},
				cent_name:{ required: true,minlength: 2,maxlength: 200},
				std_code: { required: true,digits:true, minlength:2,maxlength:10},
				phone_res: { required: true,digits:true, minlength:2,maxlength:10},
				center_supdt: "required",
				mobile_centsupdt: { required: true,digits:true, minlength:10,maxlength:10},
				exam_incharge: "required",
				mobile: { required: true,digits:true, minlength:10,maxlength:10},
				email : {required : true, maxlength : 100, email : true},
				cent_add1: "required",
				cent_add2:"required",
				district_id: "required",
				pin:{required: true,digits:true, minlength:6,maxlength:10},
				police_station:"required",
				accountno:{required: true,digits:true, minlength:2,maxlength:25},
				bank_name: "required",
				bank_ifsc:{required: true,minlength:6,maxlength:25}
			},
			errorElement: 'div', 
			messages: {
				
				mobile: {
				required: "Please enter contact number",
				minlength: "The contact number should be 10 digits",
				digits: "Please enter only numbers",
				maxlength: "The contact number should be 10 digits",
				},
				mobile_centsupdt: {
				required: "Please enter contact number",
				minlength: "The contact number should be 10 digits",
				digits: "Please enter only numbers",
				maxlength: "The contact number should be 10 digits",
				},
				email : {
				required : 'Enter Email Detail',
				email : 'Enter Valid Email Detail',
				maxlength : 'Email should not be more than 50 character'
				},
				stream: "Please select the Stream Type",
				ecenter10: {
				required: "Please Enter the exam center 10th",
				digits: "Please enter only numbers",
				minlength: "The ecenter10  should be 2 digits",
				maxlength: "The ecenter10  should be 10 digits",
				},
				ecenter12: {
				required: "Please Enter the exam center 12th",
				digits: "Please enter only numbers",
				minlength: "The ecenter12  should be 2 digits",
				maxlength: "The ecenter12  should be 10 digits",
				},
				capacity: {
				required: "Please Enter the Capacity",
				digits: "Please enter only numbers",
				minlength: "The Capacity  should be 2 digits",
				maxlength: "The Capacity  should be 10 digits",
				},
				cent_name: "Please Enter the Center name",
				std_code: {
				required: "Please Enter the std code",
				digits: "Please enter only numbers",
				minlength: "The std code  should be 2 digits",
				maxlength: "The std code  should be 10 digits",
				},
				phone_res: {
				required: "Please Enter the phone res",
				digits: "Please enter only numbers",
				minlength: "The phone res  should be 2 digits",
				maxlength: "The phone res  should be 10 digits",
				},
				center_supdt: "Please Enter the center supdt",
				exam_incharge: "Please Enter the exam incharge",
				cent_add1: "Please Enter the center address 1",
				cent_add2: "Please Enter the center address 2",
				district_id: "Please select the district Type",
				pin: {
				required: "Please Enter the pin code",
				digits: "Please enter only numbers",
				minlength: "The pin code  should be minimum 6 digits",
				maxlength: "The pin code  should be maximum 6 digits",
				},
				police_station: "Please Enter the Year Pass Type",
				accountno: {
					required: "Please Enter the account number",
					minlength: "The Account number should be minimum 2 digits",
					digits: "Please enter only numbers",
					maxlength: "The Account number should be maximum 25 digits",
				},
				bank_name: "Please Enter the bank name",
				bank_ifsc: {
					required: "Please Enter the bank ifsc",
					minlength: "The bank ifsc should be minimum 2 digits",
					maxlength: "The bank ifsc should be maximum 25 digits",
				},

				
			},
			success: function(response){
			},
			submitHandler: function(form) {
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.ajaxCheckSsoAlreadyExamCenter,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response.status == false){
								message = JSON.stringify(response); 
								var errStr = "";
								var counter = 1;
								$.each(response, function(index, elementOuter) {
									$.each(elementOuter, function(index, elementt) {
										$.each(elementt, function(indexiDis, elementtiDis) { 
											errStr += "" + counter + " : " + elementtiDis  + "\n";
											counter++;
										});
									});
								});
										
								swal({
									title: "Validation Error",
									text: errStr,
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
									title: 'Are you sure you want to save your information.',
									text: "You won't be able to revert this!",
									icon: 'success',
									buttons: true,
								})
								.then((willsave) => {
									if (willsave) { 
										$(formId).addClass(clsName);
										$('#ajaxRequest').val('0') 
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
				}else {
					return true;
				} 
			}
		});
	});
	
// </script>



