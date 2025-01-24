
	$(document).ready(function() { 
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		var action = "ajaxSuppSubjectValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
		
		/*
		$('.show_confirm').click(function (event) {
			alert("click function");
			return false;
		});
		*/
		
		$('.show_confirm').validate({
			alert("test123");
			success: function(response){
				
			},
			/*
			submitHandler: function(form) {
				var clsName = "api";
				$('.subject_list').prop("disabled", false);
		
				if($(formId).hasClass(clsName)){
					return true;
				} else {
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
			*/
			submitHandler: function(form) {  
				alert("test1234567");
				$(".subjectitem").prop("disabled", false );
				if($('#ajaxRequest').val()==1){
					showLoading();
					$.ajax({
						url: config.routes.ajaxSuppSubjectValidation,
						type: "POST",
						data: $(formId).serialize(),
						success: function (response) { 
							hideLoading();
							if(response.isValid==false){
								var message = response.error;
								swal({
									title: "Validation Error",
									text: message,
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
									//text: "You won't be able to revert this!",
									text: form_edit_msg,
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
			}
		});
		
		$('.edit_supp_btn').on("click",function(event){
			var edit_supp_btn_id = $(this).attr('id');
			if(edit_supp_btn_id!=''){
				$('.'+edit_supp_btn_id).prop( "disabled", false);
			}
		});
	
	}); 
	
	
	   