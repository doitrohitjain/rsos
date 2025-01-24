
 $(document).ready(function() {
		formId = "#" + config.data.formId;
		$(formId).validate({
			rules: {
				 course: "required",
				 subjects: "required",
				 exam_time_start: "required",
				 exam_time_end: "required",
				 exam_date: "required",
				 stream: "required",
			},
			
			messages: {
				course: "Course is required",
				subjects: "Subjects is required",
				exam_time_start: "Exam Time Start is required",
				exam_time_end: "Exam Time End is required",
				exam_date: "Exam Date is required",
				stream: "Stream is required",
					
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
	
$(function(){
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxCoursesubjects";
	var ajaxUrl = ajaxBaseUrl + action;
	$('.course').on('change', function(){
	var course = $(".course").val();
	if(course!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.ajaxCoursesubjects,
			data: { course:course },
			success: function(data) { 
				var toAppend = '';
				toAppend += '<option>Select विषय (Subjects) </option>';
				$.each(data,function(i,o){ 
				   toAppend += '<option value=' + i +'>'+o+'</option>';
				}); 
				$('#subjectstype')
					.find('option')
					.remove()
					.end()
					.append(toAppend);  
			}
		});
	}
});
});




