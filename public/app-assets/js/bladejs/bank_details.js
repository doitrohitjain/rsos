    // jQuery Form Validator
	 $(document).ready(function() {  
		var is_dgs_student = $("#is_dgs_student").val();
		
		$("input[type=text]").keyup(function () { //
			$(this).val($(this).val().toUpperCase());
		}); 
		$("input[type=text]").blur(function () { //
			$(this).val($(this).val().toUpperCase());
		}); 
		$('.uppercase').each(function(index, element) {
			var ov = $(this).val();
			var nv = $(this).val().toUpperCase();
			$(this).val(nv);
		});
		formId = "#" + config.data.formId;
		$(".show_confirm").removeAttr("disabled");
		
		if(is_dgs_student && is_dgs_student == 1){
			$(formId).validate({
				rules: {
					account_holder_name: {  minlength: 1,maxlength: 100},
					branch_name: { minlength: 1,maxlength: 100},
					account_number: { digits:true,minlength: 10 ,maxlength: 25},
					linked_mobile: { digits:true, minlength:10,maxlength:10}
				}, 
				messages: {
					account_holder_name: { 
						// required: "Account Holder Name is required", 
						minlength: "Account Holder Name must be of 1 charector",
						maxlength: "Account Holder Name cannot be more than 100 charector",
					},
					branch_name: { 
						// required: "Branch Name is required", 
						minlength: "Branch Name must be of 1 charector",
						maxlength: "Branch Name cannot be more than 100 charector",
					},
					account_number: { 
						// required: "Please enter account number", 
						minlength: "The account number should be 10 digits",
						digits: "Please enter only numbers",
						maxlength: "The account number should be 25 digits",
					},
					linked_mobile: {
						// required: "Please enter contact number",
						minlength: "The contact number should be 10 digits",
						digits: "Please enter only numbers",
						maxlength: "The contact number should be 10 digits",
					}
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
								$('.show_confirm').prop('disabled', true);
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
		}else{
			$(formId).validate({
				rules: {
					account_holder_name: { required: true, minlength: 1,maxlength: 100},
					branch_name: { required: true, minlength: 1,maxlength: 100},
					account_number: { required: true,digits:true,minlength: 10 ,maxlength: 25},
					linked_mobile: { required: true,digits:true, minlength:10,maxlength:10},
					ifsc_code: "required",
					bank_name: "required",
					state_id: "required",
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
					},
					bank_name: "Bank Name is required",
					state_id: "State Name is required",
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
								$('.show_confirm').prop('disabled', true);
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
		}
		
		function chunkSubstr(str, size) {
		  const numChunks = Math.ceil(str.length / size)
		  const chunks = new Array(numChunks)

		  for (let i = 0, o = 0; i < numChunks; ++i, o += size) {
			chunks[i] = str.substr(o, size)
		  }

		  return chunks
		} 
	
	 

  $('.bank_name_state').on('change', function(){
	var bank_id = $("#bank_name").val(); 
	var state_id = $("#state_id").val(); 
	if(bank_id != '' && state_id != ''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.getListBanksIfscCode,
			data: { bank_id: bank_id, state_id: state_id},
			success: function (data) { 
					$('#ifsc_code').find('option').attr('selectedIndex', '-1'); 
					var toAppend = '';
					toAppend += '<option>चयन राज्य (Select IFSC) </option>'; 
					$('#ifsc_code')
						.find('option')
						.remove()
						.end()
						.append(toAppend); 
					$("#ifsc_code").trigger('contentChanged');
					$.each(data, function(key,value) {
						// var valueLenght = value.length;
						// var checkData =  new Array();
						// if(valueLenght > 20){
							// checkData = chunkSubstr(value,10); 
							// value = checkData = checkData.join("\r\n");
						// } 
						$('#ifsc_code').append('<option value="' + key + '">' + value + '</option>');
						$("#ifsc_code").trigger('contentChanged');
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


 $('#ifsc_code').on('change', function(){
	var ifsc_code = $("#ifsc_code").val();
	if(ifsc_code!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.getBankBranchDetails,
			data: { ifsc_code: ifsc_code},
			success: function (data) { 
			  $('#branch_name').val(data.BRANCH);
			  $('#branch_address').val(data.BRANCH_ADDRESS);
			  $('#MICR').val(data.MICR);
                hideLoading();
                return false;
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
		});
	}
});

});
	
// </script>