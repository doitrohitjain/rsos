//Just below in laravel
function getEnrollments(aicode,course,stream,supp,examcenterdetailid,centerallotmentid){
	$("#allottedenrollmentdiv").html('<span style="font-weight: bold;font-size:25px;">Data Not Found</span>'); 
	showLoading(); 
	var action = "ajaxviewenrollments";
	var ajaxUrl = ajaxBaseUrl + action;
	// alert(aicode); return false;
	
	if(aicode < 10000){
		var aicode = '0'+ aicode;
	} else {
		var aicode = aicode;
	}
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		url: config.routes.ajaxviewenrollments,
		type: "post",
		dataType: "html",
		data: {aicode:aicode,course:course,stream:stream,supp:supp,examcenterdetailid:examcenterdetailid,centerallotmentid:centerallotmentid},
		success: function (response,data) {
			hideLoading();
			$('.allottedenrollmentdiv').show();
			$("#allottedenrollmentdiv").html('<span style="font-weight: bold;font-size:25px;">Alloted Enrollment Students</span><br><br>'+response); 
		},
		error: function (data) {
			console.log('Error:', data); 
			hideLoading();
		}
	});
	
	showLoading(); 
	var action = "ajaxViewCreateDate";
	var ajaxUrl = ajaxBaseUrl + action;
	
	$.ajax({
		url: config.routes.ajaxViewCreateDate,
		type: "post",
		dataType: "html",
		data: {aicode:aicode,course:course,stream:stream,supp:supp,examcenterdetailid:examcenterdetailid,centerallotmentid:centerallotmentid},
		success: function (response,data) {
			hideLoading(); 
			$("#allotmetndatadiv").html('<span style="font-weight: bold;font-size:20px;color:blue;">Allotment date ('+response+')</span>'); 
		},
		error: function (data) {
			console.log('Error:', data); 
			hideLoading();
		}
	});
	
} 

/* Need to be update end */


$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#" + config.data.formId;

	var action = "ajaxTocValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	
	/*
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			showLoading(); 
			$.ajax({
				url: config.routes.ajaxTocValidation,
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
						var clsName = "api";

						if($(formId).hasClass(clsName)){

						return true;
						}
						swal({
							title: 'Are you sure save your information.',
							text: "You won't be able to revert this!",
							// text: form_edit_msg,
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
		}
	});
	*/
}); 