
$(document).ready(function() {
	$('.collapsible').collapsible({
		accordion:false
	}); 
	
	$(".btn_disabled").removeAttr("disabled");
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#" + config.data.formId;
	var action = "ajaxDocumentVerificationValidation";
	var ajaxUrl = ajaxBaseUrl + action;
	
		$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val() == 1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxDocumentVerificationValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						$("#isAllRejected").val(response.isAllRejected);
						if(response.isValid == false){
							message = JSON.stringify(response.error);
							var errStr = "";
							var counter = 1;
							$.each(response.error, function(index, elementOuter) {
								errStr += "" + counter + " : " + elementOuter  + "\n";
								counter++;
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
							lowerText = " You won't be able to revert this! ";
							var extra = ""; 
							if(response.extra != "" || response.extra != null ||  response.extra != "null"){
								extra = response.extra;
								lowerText = extra + " You won't be able to revert this! ";
							}
							if($(formId).hasClass(clsName)){ 
								return true;
							} 
							swal({
								title: 'Are you sure save your information.',
								text: lowerText,
								icon: 'success',
								buttons: true,
							})  
							.then((willsave) => {
								if (willsave) { 
									$('.btn_disabled').prop('disabled', true);
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

$('.request_btn').on('click', function () {
	showLoading(); 
}); 


$('#chkboxactionmain').on('click', function () {
	if ($(this).is(":checked")){
		$('.chkboxaction').prop("checked", true); 
		$(".singleCheckboxAction").html('<span class="chip lighten-5 green green-text">Approve</span>');
	}else{
		$('.chkboxaction').prop("checked", false); 
		$(".singleCheckboxAction").html('<span class="chip lighten-5 red red-text">Reject</span>');
	}	
});

$('.chkbox').on('click', function () {	
	var uln = $(this).attr("data-uln");
	var lln = $(this).attr("data-lln");

	var mainLblId = "#mlbl" + uln;
	var allInUpperSection = ".uln" + uln;
	$(mainLblId).html("");
	var isAllChecked = true;
	
	$(allInUpperSection).each(function() {
		if ($(this).is(":checked")){}else{
			isAllChecked = false;
		}
	});
	
	
	if(isAllChecked){
		$(mainLblId).html('<span class="chip lighten-5 green green-text">Approve</span>');
	}else{
		$(mainLblId).html('<span class="chip lighten-5 red red-text">Reject</span>');
	}
	return true;
});