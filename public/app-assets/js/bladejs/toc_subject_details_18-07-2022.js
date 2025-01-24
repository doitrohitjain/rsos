	
	$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
	
		var action = "ajaxTocValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
		
		var action2 = "ajaxRsosFailYearsList";
		var ajaxUrl2 = ajaxBaseUrl + action2;
		
		$(formId).validate({
			success: function(response){
				
			},
			submitHandler: function(form) { 
				if($('#ajaxRequest').val()==1 && $('.is_toc').val()!=0){
					showLoading(); 
					$.ajax({
						url: config.routes.ajaxTocValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response.isValid==false){
								var message = response.error;
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
									//text: "You won't be able to revert this!",
									text: form_edit_msg,
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
					var clsName = "api";

					if($(formId).hasClass(clsName)){
						return true;
					}
					swal({
						title: 'Are you sure save your information.',
						//text: "You won't be able to revert this!",
						text: form_edit_msg,
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
			}
		});
	});
	
	
	$('.is_toc').on("select2:select", function(event) { 
		var value = $(event.currentTarget).find("option:selected").val();
		$('.toc-section-field').val('');
		$('.toc-section-field-select').find("option:selected").val('');
		$('.board-section').hide();
		$('.pass-year-section').hide();
		$('.fail-year-section').hide();
		$('.toc-section').hide();
		if(value=='1'){ 
			showLoading();
			$('.board-section').show();
			hideLoading();
		}
	});
	
	$('.board').on("select2:select", function(event){
		var board = $(this).val();
		var adm_type = $('.adm_type').val();
		var stream = $('.stream').val();
		
		$('.toc-section-field').val('');
		$('.toc-section-field-select').find("option:selected").val('');
		$('.pass-year-section').hide();
		$('.fail-year-section').hide();
		$('.toc-section').hide();
		
		// 02072022
		$('.toc_subject_dropdown').find("option:selected").val('');
		$('.toc_total_calculation').val('');
		// 02072022
		
		$('.year_fail_field').html('<option value="">Select Year</option>');
		
		showLoading();
		
		$.ajax({
			url: config.routes.ajaxshowPassFieldToc,
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {'adm_type': adm_type,'stream': stream,'board': board},
			success: function (response){ 
				// response = 
				if(response==1){ 
					showLoading();
					$('.pass-year-section').show();
					$('.toc-section').show();
					hideLoading();
				}else {
					showLoading();
					$('.fail-year-section').show();
					$('.toc-section').show();
					hideLoading();
				} 
			},
			error: function (data) {
				console.log('Error:', data); 
				hideLoading();
			}
		});
		
		// initialize
		$('.year_fail_field').formSelect();
		
		// setup listener for custom event to re-initialize on change
		$('.year_fail_field').on('contentChanged', function() {
			// $(this).material_select();
			$(this).formSelect();
		});
		
		showLoading();
		event.preventDefault();
		$.ajax({
			url: config.routes.ajaxRsosFailYearsList,
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {'board': board},
			success: function (data) { 
				$.each(data, function(key,value) {
					$('.year_fail_field').append('<option value="' + key + '">' + value + '</option>');
					$(".year_fail_field").trigger('contentChanged');
				});	
				hideLoading();
			},
			error: function (data) {
				console.log('Error:', data); 
				hideLoading();
			}
		});
	});
	
	
	$('.toc_total_calculation').on("keyup", function() {  
		var id = $(this).attr('id').replace(/[^0-9.]/g, "");
		
		var theory_marks = 0;
		if($('#theory_'+id).val()!=''){
			var theory_marks = parseInt($('#theory_'+id).val());
		}
		
		var practical_marks = 0;
		if($('#practical_'+id).val()!=''){
			var practical_marks = parseInt($('#practical_'+id).val());
		}
		
		if(parseInt(theory_marks+practical_marks) > 100){
			$(this).val('');
			
			swal({
				title: 'Error : ',
				text: 'Total marks should be less than or equal 100.',
				icon: 'error',
				buttons: true,
			});
			
			$('#total_'+id).val('');
			return false;
		}
		
		$('#total_'+id).val(parseInt(theory_marks+practical_marks));
	});
	
	
	$('.toc_subject_dropdown').on("select2:select", function(event){
		$('select').formSelect();
		
		var subject_id = $(this).val();
		var subjectsrno = $(this).attr('subjectsrno');
		// var subjectsrno = $(this).find('select2:select').attr('subjectsrno');
		// var subjectsrno = $(this.currentTarget).find("option:selected").val();
		// alert(subjectsrno);
		
		if(subject_id!=''){
			showLoading();
			$('#practical_'+ subjectsrno).prop('readonly',false);
			event.preventDefault();
			$.ajax({
				url: config.routes.ajaxIsPracticalSubject,
				type: "post",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {'subject_id': subject_id},
				success: function (data) { 
					if(data==false){
						$('#practical_'+ subjectsrno).prop('readonly','readonly');
					} 	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	
	
	$('.toc_roll_no').on("change", function(event){
		var toc_roll_no = $(this).val();
		var board = $('.board').val();
		// alert(toc_roll_no);
		
		$('.toc_roll_no_error').html('');
		if(toc_roll_no!='' && board==81){
			showLoading();
			
			$.ajax({
				url: config.routes.ajaxIsVerifyTocEnrollemnt,
				type: "post",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {'toc_roll_no': toc_roll_no},
				success: function (res) { 
					if(res==0){
						$('.toc_roll_no').val('');
						swal({
							title: 'Error : ',
							text: 'Entered Roll Number does not exist in RSOS database.',
							icon: 'error',
							buttons: true,
						});
						//$('.toc_roll_no_error').html('Entered Roll Number does not matched in our database.');
						//setTimeout(function(){  $('.toc_roll_no_error').html('');  }, 30000);
					} else {
						swal({
							title: 'Success : ',
							text: 'Roll Number is verified with our database.',
							icon: 'success',
							buttons: true,
						});
						$('.toc_roll_no_error').html('');
					} 	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
			
		}
	});
	
		
	
	
	
	
	
	




