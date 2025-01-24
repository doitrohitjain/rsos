$(document).ready(function() {
	$('.btnSubmit').addClass("hide");
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxAllotingCopiesExaminerValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "AllottingExaminationCopies";
	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) {
			btnText = "Submit";
			newBtnText = "Please wait";
			$('.btnSubmit').text(newBtnText);
			$('.btnSubmit').prop('disabled', true);
			if($('#ajaxRequest').val() == 1 ){ 
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxAllotingCopiesExaminerValidation,
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
		var course = this.value;
		$(".subjectsid").html('');
		$.ajax({
			url: config.routes.getsubjects,
			type: "get",
			data: {'id': course},
			dataType : 'json',
			success: function (result){
				$(".subjectsid").html('<option value="">Select subjects</option>');
				$.each(result,function(key,value){
					$('.subjectsid').append('<option value="' + key + '">' +value+'</option>');
					$(".subjectsid").trigger('contentChanged');
				});	
			},
		});
	});


	$('.students_appearing_fields').on('change',function() { 
		if( $(".exam_details_id").val()!='' && $(".course_id").val()!='' && $(".subjectsid").val()!=null){
			var exam_center_id=$(".exam_details_id").val();
			var course_id=$(".course_id").val();
			var subjects_id=$(".subjectsid").val();
		    $('.marking_absent_student_id').val();
		 	$('.totalapperingstudent').val('');
		 	$('.total_absent').val('');
			$('.total_nr').val(''); 
			$('.totalcopiesofsubjects').val(''); 
			showLoading(); 
			$.ajax({
				url: config.routes.getallotssoid,
				type: "get",
				data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id': subjects_id},
				dataType : 'json',
				success: function (result){
				   if(typeof(result.ssoid)  === "undefined"){}else{ 
					   $('.examiner_name').val(result.name);
					   $('.mobile').val(result.mobile);
					   $('.mapping_examiner_id').val(result.id);
					   $('.ischanged').prop("checked", true); 
					   $('.sso_id').val(result.ssoid).trigger('change');
					   $(".isChangedCls").removeClass("hide");
				   }
			   },error: function (response) {
					console.log('Error:', response); 
					hideLoading();
				   	return false;
				}
			});	
			$.ajax({
				url: config.routes.getDataMarkingAbsentStudent,
				type: "get",
				data: {'exam_center_id': exam_center_id,'course_id': course_id,'subjects_id': subjects_id},
				dataType : 'json',
				success: function(result){ 
					$('.btnSubmit').removeClass("hide"); 
					if(result != '1'){
						$(".totalapperingstudent").val(result.total_students_appearing); 
					    $(".totalcopiesofsubjects").val(result.total_copies_of_subject);
					    $(".total_absent").val(result.total_absent); 
					    $(".total_nr").val(result.total_nr);
					    $('.marking_absent_student_id').val(result.id); 
						hideLoading();
						return false;
					}else{ 
						
						if($('.btnSubmit').hasClass("hide")){

						}else{
							$('.btnSubmit').addClass("hide");
						}
						$('.sso_id').val("").trigger('change');
						swal({
							title: 'Either the selected combination yet not marked absent by Secrecy Department or student not found.',
							text: "",
							icon: 'error',
							buttons: {OK: true},
						})
						 .then((willsave) => {
							
						});
					}
					
				},error: function (response) {
					console.log('Error:', response); 
					hideLoading();
					return false;
				}
			});
		} else {
			return true;
		}
	}); 
  
	$(".sso_id").change(function(){
		
		$('.examiner_name').val('');
		$('.mobile').val('');
		$('.mapping_examiner_id').val('');
		if($(this).val()!=''){
			showLoading(); 
			$.ajax({
			url: config.routes.getTheoryExaminer,
			type: "get",
			dataType: "json",
			data: {'sso_id':$(this).val()},
			success: function (response) {
				
				if(response!='1'){
					$('.examiner_name').val(response.name);
					$('.mobile').val(response.mobile);
					$('.mapping_examiner_id').val(response.id);
					hideLoading();
					return false;
				}else{
					swal({
						title: "SSO doesn't not Mapped.",
						text: "",
						icon: 'error',
							buttons: {OK: true},
						})
					.then((willsave) => {
						
					});
				}
			},error: function (response) {
					console.log('Error:', response); 
					hideLoading();
					return false;
				}
			});
		} else {
			return true;
		}
	}); 
});
