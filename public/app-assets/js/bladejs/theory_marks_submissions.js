	$(document).ready(function() { 
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + "marks_submissions_id";
		
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
						url: config.routes.ajaxMarkSubmmisionsValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response.isValid==false){ 
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
									title: 'Are you sure want yo save your information.',
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


	$('.theory_absent').on("click", function(event) {
		var theory_absent_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		var theory_absent_value = $("input[id='theory_absent"+theory_absent_row_id+"']").val();
		
		$('.final_theory_marks_'+theory_absent_row_id).prop('readonly',false);
		$('.final_theory_marks_'+theory_absent_row_id).prop('required',true);
		if ($("input[id='theory_absent_"+theory_absent_row_id+"']").is(":checked")){
			$('.final_theory_marks_'+theory_absent_row_id).val('');
			$('.final_theory_marks_'+theory_absent_row_id).prop('readonly', true);
			$('.theory_absent_nr_'+theory_absent_row_id).prop('checked', false);
			$('.final_theory_marks_'+theory_absent_row_id).prop('required',false);
		} 
	});

	$('.theory_absent_nr').on("click", function(event) {
		var theory_absent_nr_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		var theory_absent_nr_value = $("input[id='theory_absent_nr"+theory_absent_nr_row_id+"']").val();
		 
		$('.final_theory_marks_'+theory_absent_nr_row_id).prop('readonly',false);
		$('.final_theory_marks_'+theory_absent_nr_row_id).prop('required',true);
		if ($("input[id='theory_absent_nr_"+theory_absent_nr_row_id+"']").is(":checked")){
			$('.final_theory_marks_'+theory_absent_nr_row_id).val('');
			$('.final_theory_marks_'+theory_absent_nr_row_id).prop('readonly', true); 
			$('.theory_absent_'+theory_absent_nr_row_id).prop('checked', false);
			$('.final_theory_marks_'+theory_absent_nr_row_id).prop('required',false);
		} 
	});
	
	$('.final_theory_marks').on("change", function(event) { 
		min_marks = parseInt(min_marks);
		max_marks = parseInt(max_marks);
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
	});
	
	$('.update_absent_nr').on("click", function(event) { 
		$('.absent_nr_div').show();
		$('.final_theory_marks_span').html('');
	});
	
	



