    // jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + config.data.formId;
		$(formId).validate({
			rules: {
				 account_holder_name: { required: true, minlength: 1,maxlength: 100},
				 branch_name: { required: true, minlength: 1,maxlength: 100},
				 account_number: { required: true,digits:true,minlength: 10 ,maxlength: 25},
				 linked_mobile: { required: true,digits:true, minlength:10,maxlength:10},
				 ifsc_code: {required: true,minlength:2,maxlength: 25},
				 bank_name: "required",
			},
			
			messages: {
				account_holder_name: { 
					required: "Account Holder Name is required", 
					minlength: "Account Holder Name must be of 1 charector",
					maxlength: "Account Holder Name cannot be more than 100 charector",
				},
				branch_name: { 
					required: "Branch Name is required", 
					minlength: "Branch Name must be of 1 charector",
					maxlength: "Branch Name cannot be more than 100 charector",
				},
				account_number: { 
					required: "Please enter account number", 
					minlength: "The account number should be 10 digits",
				    digits: "Please enter only numbers",
				    maxlength: "The account number should be 25 digits",
				},
				linked_mobile: {
				required: "Please enter contact number",
				minlength: "The contact number should be 10 digits",
				digits: "Please enter only numbers",
				maxlength: "The contact number should be 10 digits",
				},
				ifsc_code: { 
					required: "IFSC Code is required", 
					minlength: "The IFSC Code should be 25 digits",
				    maxlength: "The IFSC Code should be 25 digits",
				},
				bank_name: "Bank Name is required",
				
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