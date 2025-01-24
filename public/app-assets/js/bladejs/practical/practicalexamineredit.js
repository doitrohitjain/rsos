$(document).ready(function() { 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxEditPracticalValidation";
	formId = "#" + "practicalexamineradd";
	
	$(formId).validate({
		/* rules: {
			ssoid: { required: true , minlength: 2, maxlength: 50 },
			name: { required: true , minlength: 2, maxlength: 50 },
			email : { required: true , email: true},
			mobile : { required: true , number:true  ,minlength: 10,maxlength: 12},
			college_name : { required: true , minlength: 2, maxlength: 150 }
		},
		messages: {
			ssoid: { 
				required: "SSO ID is required",
				minlength: "SSO ID must be 2 min charector",
				maxlength: "SSO ID must be less than 50 charector"
			},
			name: { 
				required: "Full Name is required",
				minlength: "Full Name must be 2 min charector",
				maxlength: "Full Name must be less than 50 charector"
			},
			email: {
				required: "Email is required", 
			},
			mobile: { 
				required: "Mobile is required", 
				minlength: "Mobile must be 10 min charector",
				maxlength: "Mobile must be less than 50 charector",
				number: "Mobile must be numeric"
			},
			college_name: {
				required: "School Name is required",
				minlength: "School Name must be 2 min charector",
				maxlength: "School Name must be less than 150 charector"
			},
		},*/
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){ 
				showLoading();    
				$.ajax({
					url: config.routes.ajaxEditPracticalValidation,
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
	
	
	$(".ssoid").change(function(){
		$('.name').val('');
		$('.email').val('');
		$('.mobile').val('');
		var ssoid = $(this).val();
		if($(this).val()!=''){
			showLoading(); 
			$.ajax({
				url: config.routes.getSSOIDDetialsByMappingExaminerTbl,
				type: "get",
				dataType: "json",
				data: {'sso_id':$(this).val(),'isCheckAllRoles':true},
				success: function (response) {
					
					$false = false;
					if($false){
					// if(response.status == true){
						$('.name').val(response.name);
						$('.email').val(response.email);
						$('.mobile').val(response.mobile);
						$('.college_name').val(response.college_name);   
						hideLoading();
						return false;
					} else {  
						$.ajax({
							url: config.routes.ajaxGetSSOIDDetials,
							type: "get",
							dataType: "json",
							data: {'sso_id':ssoid},
							success: function (response) {
								if(response!=null){
									$('.name').val(response.displayName);
									$('.email').val(response.mailPersonal);
									$('.mobile').val(response.mobile);
									hideLoading();
									return false;
								} else {
									swal({
										title: "Validation Error",
										text: 'Please enter valid SSO ID.',
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
	
	
	$('.district_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();
		$('.deo_id').html('<option value="">Select जिला शिक्षा अधिकारी (Deo Name)</option>');
		$('.deo_id').val('');
		
		if(id!=''){ 
			showLoading();
			// initialize
			$('.deo_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.deo_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
		
			event.preventDefault();
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: config.routes.getDeoListByDistrictId,
				type: "post",
				data: {'id': id},
				success: function (data) {  
					$.each(data, function(key,value) {
						$('.deo_id').append('<option value="' + key + '">' + value + '</option>');
						$(".deo_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	
	
});



