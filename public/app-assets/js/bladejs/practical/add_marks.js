// <script type="text/javascript">
	// jQuery Form Validator
	
	$(document).ready(function() { 
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		
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
						url: config.routes.ajaxPracticalValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response.isValid==false){ 
								var message = response.errors;
								
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
									title: 'Are you sure save your information.',
									text: "You won't be able to revert this!",
									icon: 'success',
									buttons: true,
								})
								.then((willsave) => {
									if (willsave) {
								$(formId).addClass(clsName); 
										$('#ajaxRequest').val('0');
										$('.submit_dsiabled').prop('disabled', false);
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
	
	// jQuery Form Validator
	$('.practical_absent').on("click", function(event) {
		var particuler_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		var practical_absent_value = $("input[id='practicalAbsent"+particuler_row_id+"']").val();
		
		
		$('.final_practical_marks_'+particuler_row_id).attr('readonly',false);
		
		if ($("input[id='practicalAbsent"+particuler_row_id+"']").is(":checked")){
			$('.final_practical_marks_'+particuler_row_id).val('');
			$('.final_practical_marks_'+particuler_row_id).attr('readonly',true);
			$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
			});
			$.ajax({
			type:'post',
			url: config.routes.updatePraticalMarks,
			data: {'id': practical_absent_value,'absent':1},
			success: function (result){
				
			},
		});
		}
		
	});
	
	
	$('.final_practical_marks').on("keyup", function(event) {
		var particuler_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		$('.practical_absent_'+particuler_row_id).attr('checked',false);
	});
	
	
	
	
	
	$(document).on("focusout",".final_practical_marks",function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		var action = "updatePraticalMarks";
		var ajaxUrl = ajaxBaseUrl + action;
		var particuler_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		var getvalue = $(this).val();
		
		
		var row_id = '';
			if($(this).val()=='' || $(this).val()=='undefined') { 
				$(this).val('');
				
				// $(this).focus();
				return false;
			}
			min_marks = parseInt(min_marks);
			min_marks = 0;
			max_marks = parseInt(max_marks);
			input_marks  = parseInt($(this).val());  
			if( input_marks >= min_marks &&  input_marks <= max_marks) {
			
			} else { 
				swal({
					title: "Validation Error",
					text: 'Please enter marks should be from ' + min_marks + " to " + max_marks + ' marks.',
					icon: "error",
					button: "Close",
					timer: 30000
				});	
				$(this).val('');
				$(this).focus();
				return false;

			}


			if( parseInt($(this).val()) > parseInt((parseInt(min_marks)-1)) && parseInt($(this).val()) < parseInt((parseInt(max_marks)+1)) ) {
				
			} else { 
				swal({
					title: "Validation Error",
					text: 'Entered marks should be less than or equal to ' + max_marks + '.',
					icon: "error",
					button: "Close",
					timer: 30000
				});	
				$(this).val('');
				$(this).focus();
				return false;
			} 
			$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
			});
			$.ajax({
			type:'post',
			url: config.routes.updatePraticalMarks,
			data: {'id': particuler_row_id,'value':getvalue},
			success: function (result){
				
			},
		});
		

	});
// </script>



