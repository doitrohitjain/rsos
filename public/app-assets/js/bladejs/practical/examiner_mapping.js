$(document).ready(function() { 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxPracticalExaminerValidation";
	formId = "#" + "PracticalExaminerForm";
	
	$(formId).validate({
		/*
		rules: {
			course: { required: true , number: true},
			examcenter_detail: { required: true , number: true},
			subject : { required: true , number: true},
			ssoid : { required: true , minlength: 2,maxlength: 50},
			examiner_name : { required: true},
			email : { required: true},
			mobile : { required: true,minlength: 6,maxlength: 12,number: true},
		},
		messages: {
			course: { 
				required: "Course is required", 
				number: "Course must be numeric"
			},
			examcenter_detail: {
				required: "Exam Center Detail is required", 
				number: "Exam Center Detail must be numeric"
			},
			subject: {
				required: "Subject is required", 
				number: "Subject must be numeric"
			},
			ssoid: {
				required: "SSO ID is required",
				minlength: "SSO ID must be of 6 charector",
				maxlength: "SSO ID cannot be more than 2 charector",
			},
			examiner_name: {
				required: "Examiner Name is required",
			},
			email: {
				required: "Email is required", 
			},
			mobile: { 
				required: "Mobile is required", 
				minlength: "Mobile must be of 6 charector",
				maxlength: "Mobile cannot be more than 12 charector",
				number: "Mobile must be numeric"
			} 
		},
		*/	
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxPracticalExaminerValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						console.log(response);
						
						
						if(response.isValid==false){
							message = JSON.stringify(response); 
							var errStr = "";
							var counter = 1;
							$.each(response, function(index, elementOuter) {
								$.each(elementOuter, function(index, elementt) {  
									$.each(elementt, function(indexiDis, elementtiDis) { 
										errStr += "" + counter + " : " + elementtiDis  + "\n";
										counter++;
									});
								});
							});
								 
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
	
	$(".course").on('change',function() {
		showLoading();
		$(".examcenter_detail").html('<option value="">Select Exam Center</option>');
		$(".subject").html('<option value="">Select Subject</option>');
		
		$.ajax({
			url: config.routes.ajaxCourseExamcenters,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".examcenter_detail").append($('<option>', { value : key }).text(value)); 
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
					$(".subject").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});
	});
	
	$(".ssoid").change(function(){
		$('.examiner_name').val('');
		$('.email').val('');
		$('.mobile').val('');
		if($(this).val()!=''){
			showLoading(); 
			$.ajax({
				url: config.routes.getSSOIDDetialsByMappingExaminerTbl,
				type: "get",
				dataType: "json",
				data: {'sso_id':$(this).val()},
				success: function (response) {
					if(response.status==true){
						$('.examiner_name').val(response.name);
						$('.email').val(response.email);
						$('.mobile').val(response.mobile);
						hideLoading();
						return false;
					} else {
						swal({
							title: "Validation Error",
							text: 'SSO does not mapped as practical examiner.',
							icon: "error",
							button: "Close",
							timer: 30000
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
	
	// Get SSO Details from SSO API 
	/*
	$(".ssoid").change(function(){
		$('.examiner_name').val('');
		$('.email').val('');
		$('.mobile').val('');
		if($(this).val()!=''){
			showLoading(); 
			$.ajax({
				url: config.routes.ajaxGetSSOIDDetials,
				type: "get",
				dataType: "json",
				data: {'sso_id':$(this).val()},
				success: function (response) {
					if(response!=null){
						$('.examiner_name').val(response.displayName);
						$('.email').val(response.mailPersonal);
						$('.mobile').val(response.mobile);
						hideLoading();
						return false;
					} else {
						swal({
							title: "Validation Error",
							text: 'Please Enter the valid SSO Id.',
							icon: "error",
							button: "Close",
							timer: 30000
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
	*/
	
});



