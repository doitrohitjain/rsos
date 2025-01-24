$(document).ready(function() {
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		var action = "ajaxcreatepracticalexaminerslotValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
		var cbCount = $("input:checked").length;
		var allowedbatchcount = $("#batch_student_count").val();	
		$(formId).validate({
			success: function(response){
				
			},
			submitHandler: function(form) { 
				//return true;
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.ajaxcreatepracticalexaminerslotValidation,
						type: "POST",
						dataType: "json",
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
								
								var cbCount = $(".practical_absent:checked").length;
								var allowedbatchcount = $("#batch_student_count").val();	
							var message = null;
							if(cbCount == 0)
							{
							   message='Please select students';
							}
							else if(cbCount > allowedbatchcount)
							{
								message = 'Max limit of batch size selected';
							}
							else if(cbCount < allowedbatchcount){
							    message = 'Selected students count doesn\'t match with batch count';
							}else{
								message =null;
							}
				
							if(message != null){
								swal({
								title: "Validation Error",
								text: message,
								icon: "error",
								button: "Close",
								timer: 30000
							});
							return false;
							}else{
								
							}
								var clsName = "api";

								if($(formId).hasClass(clsName)){

								return true;
								}
								swal({
									title: 'Are you sure save your information.',
									//text: "You won't be able to revert this!",
									//text: form_edit_msg,
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
	});

  $(function() {
        	var today = new Date().toISOString().slice(0, 16);
            document.getElementsByName("date_time_start")[0].min = today;
			document.getElementsByName("date_time_end")[0].min = today;
        });
		
 $('.practical_absent').on('change', function(){
	 var cbCount = $("input:checked").length;
     var allowedbatchcount = $("#batch_student_count").val();	
	
		if(cbCount > allowedbatchcount)
		{
			this.checked = false;
			
		}

	});	
	
	$('#selectAll').on('change', function(){
		 var cbCount = $("input:checked").length;
		 var allowedbatchcount = $("#batch_student_count").val();	
		
			if(cbCount > allowedbatchcount)
			{
				this.checked = false;
				
			}
			else if(this.checked){
			$('.practical_absent').each(function(){
				this.checked = true;
			});
			apendVal=$('.practical_absent:checked').length;
			$("#batch_student_count").val(apendVal);
			$("#batch_student_count").prop("readonly",true); 
		
		}
		else{
			$('.practical_absent').each(function(){
			this.checked = false;
			});
			apendval= null;
			$("#batch_student_count").val(apendval);
			$("#batch_student_count").prop("readonly",false);
		 
		}

	});	
	
	
//$("#selectAll").click(function(){
	//if(this.checked){
	//	$('.practical_absent').each(function(){
		//	this.checked = true;
		//});
		//apendVal=$('.practical_absent:checked').length;
		//$("#batch_student_count").val(apendVal);
	//}
	//else{
		 //$('.practical_absent').each(function(){
		//	this.checked = false;
		//});
		//apendval= null;
		//$("#batch_student_count").val(apendval);
	//}
//});

//$('.practical_absent').on('click',function(){
	//if($('.practical_absent:checked').length == $('.practical_absent').length){
	//	remaningVal=$('.practical_absent:checked').length ;
		//$("#batch_student_count").val(remaningVal);
		//$('#selectAll').prop('checked',true);
		
		
	//}else{
		//remaningVal=$('.practical_absent:checked').length ;
		
		//$('#selectAll').prop('checked',false);
		
	//}
//});
	

   