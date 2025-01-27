	
	
	$(document).ready(function() {
		formId = "#" + config.data.formId;
		
		$(formId).validate({
			rules: {
				ai_code: { required: true,number: true},
				student_supp_10: { required: true,number:true, minlength: 1,maxlength: 6},
				student_supp_12: { required: true,number:true, minlength: 1,maxlength: 6},
				student_strem1_10: { required: true,number:true, minlength: 1,maxlength: 6},
				student_strem1_12: { required: true,number:true, minlength: 1,maxlength: 6},
			},
			messages: {
				ai_code: { 
					required: "AI Code is required",
					number: "AI Code must be numeric",					
					minlength: "AI Code must be of 1 charector",
					maxlength: "AI Code cannot be more than 6 charector",
				},
				student_supp_10: { 
					required: "Supplementary-10th is required",
					number: "Supplementary-10th must be numeric",					
					minlength: "Supplementary-10th must be of 1 charector",
					maxlength: "Supplementary-10th cannot be more than 6 charector",
				},
				student_supp_12: { 
					required: "Supplementary-10th is required",
					number: "Supplementary-10th must be numeric",					
					minlength: "Supplementary-10th must be of 1 charector",
					maxlength: "Supplementary-10th cannot be more than 6 charector",
				},
				student_strem1_10: { 
					required: "Stream-1-10th is required",
					number: "Stream-1-10th must be numeric",				
					minlength: "Stream-1-10th must be of 1 charector",
					maxlength: "Stream-1-10th cannot be more than 6 charector",
				},
				student_strem1_12: { 
					required: "Stream-1-12th is required",
					number: "Stream-1-12th must be numeric",					
					minlength: "Stream-1-12th must be of 1 charector",
					maxlength: "Stream-1-12th cannot be more than 6 charector",
				}
			},
			success: function(response){
				
			},
			submitHandler: function(form) {
				
				if($('#ajaxRequest').val()==1){
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
							$('#ajaxRequest').val('0');
							$(formId).submit();
						}
					});
				}
			}
		});
	}); 
	
	
	
	$('.ai_code').on("select2:select", function(event){ 
		var aicenter = $(event.currentTarget).find("option:selected").val();
		// var aicenter = '1001';
		var stream = $('#stream').val();
		
		if(aicenter !='' ){ 
			showLoading();

			var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
			var action = "getStudentsCountForExamcenter";
			var ajaxUrl = ajaxBaseUrl + action; 
			//alert(config.routes.getStudentsCountForExamcenter);
			
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				// url: config.routes.getStudentsCount,
				url: config.routes.getStudentsCountForExamcenter,
				type: "post",
				data: {'aicenter': aicenter,'stream': stream},
				success: function (response) {
					var total = 0;
					$.each(response, function(key,value) {
						 console.log(value.total);
						 return false;
						$.each(value, function(innerkey,innerValue) {
							if(key=='remaining'){
								hideLoading();
								if(innerkey == 'supp_10'){
									$('.supp-student-10-input').val(innerValue);
									$('.supp-student-10').html(innerValue);
									total += innerValue;
									
								}
								if(innerkey == 'supp_12'){
									$('.supp-student-12-input').val(innerValue);
									$('.supp-student-12').html(innerValue);
									total += innerValue;
								}
								if(innerkey == 'stream'+stream+'_10'){
									$('.student-10-input').val(innerValue);
									$('.student-10').html(innerValue);
									total += innerValue;
								}
								if(innerkey == 'stream'+stream+'_12'){
									$('.student-12-input').val(innerValue);
									$('.student-12').html(innerValue);
									total += innerValue;
								}
							}
						});
					});
					$('.student-total-10-12').html(total);
					if(total==0){
						swal({
							title: "Error",
							text: "You have already allotted for this AI Center - Exam Center combination, You may need to be old combination delete then make entry.",
							icon: "error",
							button: "Close",
							timer: 30000
						});
					}
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	
	
