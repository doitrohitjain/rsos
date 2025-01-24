$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxMarkingAbsentValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "marking_absent"; 
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 

			btnText = "Submit";
			newBtnText = "Please wait";
			$('.btnSubmit').text(newBtnText);
			$('.btnSubmit').prop('disabled', true);
			

			if($('#ajaxRequest').val()==1){ 
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxMarkingAbsentValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response.isValid==false){ 
							errStr = response.errors;
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

			$('.btnSubmit').text(btnText);
			$('.btnSubmit').prop('disabled', false);
			

			
		}
	});
});

$(document).ready(function() {
	function showLoading() { 
		$('.mainCls').css('display', 'block');
	  } 
	function hideLoading() {
		$('.mainCls').css('display', 'none');
	}
	$('.course_id').on('change',function(){
		var id = this.value;
		$(".subjectsid").html('');
		$.ajax({
			url: config.routes.getsubjects,
			type: "get",
			data: {'id': id},
			dataType : 'json',
			success: function (result){
				$(".subjectsid").html('<option value="">Select Subject</option>');
				$.each(result,function(key,value){
					$('.subjectsid').append('<option value="' + key + '">' +value+'</option>');
					$(".subjectsid").trigger('contentChanged');
				});	
			},
		});
	});


	$('.students_appearing_fields').on('change',function() { 
		if( $(".exam_details_id").val()!='' && $(".course_id").val()!='' && $(".subjectsid").val()!=''){
			var exam_center_id=$(".exam_details_id").val();
			var course_id=$(".course_id").val();
			var subjects_id=$(".subjectsid").val();
		 	// $('.student_list_div').html('');
		 	$('.MarkingAbsentStudentTotalCopiesOfSubject').val('');
		 	$('.MarkingAbsentStudentTotalAbsent').val('');
			showLoading(); 	
			$.ajax({
				url: config.routes.get_appearing_student_count,
				type: "get",
				data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id': subjects_id},
				dataType : 'html',
				success: function(result){ 
					hideLoading();
					$(".MarkingAbsentStudentTotalCopiesOfSubject").val(result);
					$(".MarkingAbsentStudentTotalAbsent").val(result); 
					$(".total_absent").val(0); 
					$(".total_nr").val(0); 
				}
			});
		}
	});


	$('.students_appearing_fields').on('change',function() { 
		if( $(".exam_details_id").val()!='' && $(".course_id").val()!='' && $(".subjectsid").val()!=''){
			var exam_center_id=$(".exam_details_id").val();
			var course_id=$(".course_id").val();
			var subjects_id=$(".subjectsid").val();
		 	// $('.student_list_div').html('');
		 	$('.MarkingAbsentStudentTotalCopiesOfSubject').val('');
		 	$('.MarkingAbsentStudentTotalAbsent').val('');
			showLoading(); 	
			$.ajax({
				url: config.routes.checkMarkingAbsentdata,
				type: "get",
				data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id': subjects_id},
				dataType : 'json',
				success: function(result){ 
					if(result==true){
						swal({
							title: "Validation Error",
							text: "Selected Combination already marked absent.",
							icon: "error",
							button: "Close",
							timer: 30000
						}).then((result) => {
							if(result == true){
								location.reload(); // this is your location reload.
							}
					   	}) 
					}else{
						return false;
					}
				}
			});
		}
	});




	$('.get_student_list_btn').on('click',function() { 
 
		var errStr = null;
		if($(".exam_details_id").val()==''){ 
			errStr = "Please select any Exam Center.";
		}
	    if($(".course_id").val()==''){ 
			errStr = "Please select Course.";
		}
		if($(".subjectsid").val()==''){ 
			errStr = "Please select Subject.";  
			
		}
		if(errStr != null){
			swal({
				title: "Validation Error",
				text: errStr,
				icon: "error",
				button: "Close",
				timer: 30000
			});
			return false; 
		}
		var exam_center_id=$(".exam_details_id").val();
		var course_id=$(".course_id").val();
		var subjects_id=$(".subjectsid").val();
		btnText = "Get List of Students";
		newBtnText = "Please wait";
		$('.get_student_list_btn').text(newBtnText);
		$('.get_student_list_btn').prop('disabled', true);
		showLoading(); 
		 
		$.ajax({
			url: config.routes.get_appearing_student_count,
			type: "get",
			data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id': subjects_id},
			dataType : 'json',
			success: function(result){ 
				hideLoading(); 
				tempCount = result; 
				if(result <= 0){
					errStr = "Selected combinaion students not found.";
					swal({
						title: "Validation Error",
						text: errStr,
						icon: "error",
						button: "Close",
						timer: 30000
					});	
					$('.student_list_div').html("");
					$('.get_student_list_btn').text(btnText);
					$('.get_student_list_btn').prop('disabled', false);
					return false;		
				}else{


					$.ajax({
						url: config.routes.getAppearingStudent,
						type: "get",
						data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id':subjects_id},
						dataType : '',
						success: function(result){  
							hideLoading();
							$('.btnSubmit').removeClass('hide');
							$('.get_student_list_btn').text(btnText);
							$('.get_student_list_btn').prop('disabled', false);
							$('.student_list_div').html(result); 
						}
					});

				}  
			}
		}); 
		
		 
	});

	$(".MarkingAbsentStudentTotalAbsent").on('change',function() {
		$('.total_absent').val( parseInt($('.MarkingAbsentStudentTotalCopiesOfSubject').val()) - parseInt($('.total_absent').val()));
	});
	
	$(".nr_absent_fields").on('change',function() {  
		var fixcode = $(this).attr('data-fixcode');
		var id_attr = $(this).attr('id');
		if(id_attr=='absent_field_'+fixcode){
			$('#nr_field_'+fixcode).prop("checked", false);
		}
		if(id_attr=='nr_field_'+fixcode){
			$('#absent_field_'+fixcode).prop("checked", false);
		}
		var total_absent = $(".fictitious_code:checkbox:checked").length;
		
		$('.total_absent').val(total_absent);
		var total_nr = $(".NR:checkbox:checked").length;
		$('.total_nr').val(total_nr);
		$('.total_copies_of_subject').val( (parseInt($('.total_students_appearing').val()) - (parseInt($('.total_absent').val()) + parseInt($('.total_nr').val()))) );
	});
	
	
	$('.final_practical_marks').on("keyup", function(event) {
		var particuler_row_id = $(this).attr('id').replace(/[^\d.]/g,'');
		$('.practical_absent_'+particuler_row_id).attr('checked',false);
	});
	
	
});
