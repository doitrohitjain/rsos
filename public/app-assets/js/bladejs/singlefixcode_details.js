$(".course").on('change',function() {
		showLoading();
		$(".examcenter_detail").html('<option value="">Select Exam Center</option>');
		$(".subject").html('<option value="">Select Subject</option>');
		$(".examcenter_details").html('<option value="">Select Exam Center</option>');
		$(".subjects").html('<option value="">Select Subject</option>');
		
		$.ajax({
			url: config.routes.ajaxCourseExamcentersfixcode,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".examcenter_detail").append($('<option>', { value : key }).text(value));
					$(".examcenter_details").append($('<option>', { value : key }).text(value));					
				});		 		 
								 
			}
		});

		$.ajax({
			url: config.routes.ajaxCoursesubjectsfixcode,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".subject").append($('<option>', { value : key }).text(value)); 
					$(".subjects").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});
	});
	



