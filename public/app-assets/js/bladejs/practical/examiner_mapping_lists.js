$(document).ready(function() { 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxPracticalExaminerValidation";
	formId = "#" + "PracticalExaminerForm";
	
	$(".course").on('change',function() {
		showLoading();
		$(".examcenter_detail_id").html('<option value="">Select Exam Center</option>');
		$(".subject_id").html('<option value="">Select Subject</option>');
		
		$.ajax({
			url: config.routes.ajaxCourseExamcenters,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".examcenter_detail_id").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});

		$.ajax({
			url: config.routes.ajaxCoursesubjects,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".subject_id").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});
	});
});



