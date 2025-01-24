$(document).ready(function() { 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxAddDeoValidation";
	formId = "#" + "practicaldeoadd";
	
	$(formId).validate({
		/* rules: {
			ssoid: { required: true , minlength: 2, maxlength: 50 },
			name: { required: true , minlength: 2, maxlength: 50 },
			email : { required: true , email: true},
			mobile : { required: true , number:true  ,minlength: 10,maxlength: 12},
			district_id : { required: true}
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
				email: "Valid email is required",
			},
			mobile: { 
				required: "Mobile is required", 
				minlength: "Mobile must be 10 min charector",
				maxlength: "Mobile must be less than 50 charector",
				number: "Mobile must be numeric"
			},
			district_id: {
				required: "District is required",
			},
		},*/
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){ 
				showLoading();    
				$.ajax({
					url: config.routes.ajaxAddDeoValidation,
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
	
	
	$("#ssoid").change(function(){
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
	
});



