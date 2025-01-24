	
	$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
	
		var action = "ajaxTocValidation";
		var ajaxUrl = ajaxBaseUrl + action;
				
		$(formId).validate({
			/*
			rules: {
				board: { required: true},
				roll_no: { required: true}
			},
			messages: {
				board: { 
					required: "Board is required", 
				},
				roll_no: { 
					required: "Roll No. is required", 
				}
			},
			*/
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
		});
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
		
		showLoading();
		$.ajax({
			url: config.routes.ajaxshowPassFieldToc,
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {'adm_type': adm_type,'stream': stream,'board': board},
			success: function (response){ 
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
	
	
	
	
	




