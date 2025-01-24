var formId = "#allreadystudent";
var clsName = "api";
 $(document).ready(function() { 
	$('.submitBtnCls').on('click', function(event){
		var cls_enrollment = $(".cls_enrollment").val();
		var cls_dob  = $(".cls_dob ").val();
		var cls_captcha = $(".cls_captcha").val();
		var role_id = $("#role_id").val();

		

		var errorMsg = "";
		errorMsg += "Please enter ";
		temp = false;
		enrollMentTemp = false;
		enrollmentLength = cls_enrollment.length;
		 
		if(cls_enrollment == ""){
			errorMsg += " Enrollment ";
			temp = true;
			enrollMentTemp = true;
		}
		if(cls_dob == ""){
			if(temp == true){
				errorMsg += ", ";
			}
			errorMsg += "DOB";
			temp = true;
		}
		if(cls_captcha == ""){
			if(temp == true){
				errorMsg += ", ";
			}
			errorMsg += "Captcha";
		}
		if(!enrollMentTemp){
			if(enrollmentLength != 11 ){
				errorMsg += " The enrollment number should be 11 digits";
				temp = true;
			}
		}
		if(errorMsg != "Please enter "){
			swal({
				title: "Validation Error",
				text: errorMsg,
				icon: "error",
				button: "Close",
				timer: 30000
			});
			return false; 
		}else{
			event.preventDefault();
			titleText = 'Are you want to continue?(क्या आप आगे बढ़ना चाहते हैं?)',
			textText = "If your information correct then OTP will send to you registered mobile number.(यदि आपकी जानकारी सही है तो आपके पंजीकृत मोबाइल नंबर पर ओटीपी भेजा जाएगा।)",
			btnText = "Send OTP";
			
			if(role_id == 41){
				textText = "If your information correct then you will click on submit button.(अगर आपकी जानकारी सही है तो आप सबमिट बटन पर क्लिक करेंगे)";
				btnText = "Submit";
			}
			
			swal({
				title: titleText,
				text: textText,
				icon: 'success',
				buttons: ["Cancel", btnText],
			}).then((willsave) => {
				if (willsave) {
				if($('#ajaxRequest').val()==1){
				 showLoading(); 
				 $.ajax({
					 url: config.routes.checkcaptchastudent,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					 success: function (data) {
					 if(data == false){
						 swal({
							 title: 'Please enter valid captcha value.',
							 icon: 'error',
							 button: "Close",
						 });
						 $('#captcha').val('');
					 }else if(data == true) {
						 $(formId).addClass(clsName); 
						 $('#ajaxRequest').val('0');
						 $(formId).submit();
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
		}
	});
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	
	var action = "checkcaptchastudent";
	var ajaxUrl = ajaxBaseUrl + action;
	// $(formId).validate({
		// rules: {
			// dob: "required",
			// captcha: "required",
		// }, 
		// messages: { 
			// dob: "Please Enter DOB.",
			// captcha: "Please Enter Captcha.",
		// },
		// success: function(response){
		// },
		// submitHandler: function(form) { 

			
			// return true;
			// if($('#ajaxRequest').val()==1){
				// showLoading(); 
				// $.ajax({
					// url: config.routes.checkresultstudent,
					// type: "POST",
					// data: $(formId).serialize(),
					// success: function (data) {
					// if(data == false){
						// swal({
							// title: 'Result not found as per given details.',
							// icon: 'error',
							// button: "Close",
						// });
					// } else if(data == 'captchaFalse'){
						// swal({
							// title: 'Please enter valid captcha value.',
							// icon: 'error',
							// button: "Close",
							// });
						// $('#captcha').val('');
					// }else {
						// var clsName = "api";

						// if($(formId).hasClass(clsName)){

						// return true;
						// }
						// swal({
							// title: 'Are you sure you want to see result.',
							// text: "Please confirm",
							// icon: 'success',
							// buttons: true,
						// })
						// .then((willsave) => {
							// if (willsave) {
								// $(formId).addClass(clsName); 
								// $('#ajaxRequest').val('0');
								// $(formId).submit();
							// }
						// });
					// } 
					// },
					// error: function (data) {
						// console.log('Error:', data); 
						// hideLoading();
					// }
				// });
			// } else {
				// return true;
			// }
		// }
	// }); 

	 
	$('.modalCls').on('click', function(){
		var content = $(this).attr('data-content');
		var staus = toggleModel(content);
	});
	function toggleModel(content=null){
		$('#myModalInfoTooltip').modal('close');
		$('#modalContentId').html("");
		$('#modalContentId').html(content);
		$('#myModalInfoTooltip').modal('open');
		return true;
	}  
});
	

$(function() { 
	var maxBirthdayDate = new Date();
	maxBirthdayDate.setFullYear( maxBirthdayDate.getFullYear() - 12);
	var yearMaxBirthdayDate = new Date();
	yearMaxBirthdayDate.setFullYear( yearMaxBirthdayDate.getFullYear() - 25);
  
	var dobval  = $("#dob").val(); 
	$('#my_date_picker').datepicker({
		maxDate: maxBirthdayDate,
		defaultDate: yearMaxBirthdayDate 
	}); 
	if(dobval != ""){
		$('#my_date_picker').val(dobval).trigger('change');
		$('#my_date_picker').attr("disabled","disabled");
	}
	
	
	$("#captchaRefresh").click(function(){
		showLoading();
		$.ajax({
			url: config.routes.ajaxGenerateCaptcha,
			type: "GET",
			success: function (data) {
				$('#captchaImg').html(data);
				hideLoading();
			},
			error: function (data) {
				console.log('Error:', data); 
				hideLoading();
			}
		});
		hideLoading();
	});
	
	
	
	
});







