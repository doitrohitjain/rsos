$(document).ready(function() {
	var is_dgs_student = $("#is_dgs_student").val();
	$("input[type=text]").keyup(function () { //
		$(this).val($(this).val().toUpperCase());
	}); 
	$("input[type=text]").blur(function () { //
		$(this).val($(this).val().toUpperCase());
	});  
	
	$(".btn_disable").removeAttr("disabled");
	$('.sendotp').on('click', function(){ 
		var student_id = $("#tempsid").val();
		var mobile = $(".mobilenumberinput").val();
		var errMsg = null;
		if(mobile == ""){
			errMsg = "Please enter mobile number."
		}else{
			mlength = mobile.length;
			if(mlength != 10){
				errMsg = "Mobile number should be 10 digits."
			}
		}
		if(errMsg != null){
			swal({
				title: errMsg,
				icon: 'error',
				showConfirmButton: false,
				timer: 10000
			});
			return false;
		}

		if(student_id !=''){
            showLoading();
			$('.btn_disable').prop('disabled', true);
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
			url: config.routes.send_otp_to_student,
			type: "post",
			dataType: "json",
			data: {'mobile':mobile,'student_id':student_id},
			success: function (response) {
               if(response==true){
					swal({
						title: 'OTP Sent.',
						text: "OTP has been sent to your registered mobile number.",
						icon: 'success',
						showConfirmButton: true,
						timer: 3000
					}).then(() => {
						location.reload();
					});
                }      
            }
			});	
        }
	});

	$('.validateotp').on('click', function(){ 
		var otp = $(".otpinput").val();
        var student_id = $("#tempsid").val();
		var errMsg = null;
		if(otp == ""){
			errMsg = "Please enter OTP."
		}else{
			mlength = otp.length;
			if(mlength != 6){
				errMsg = "OTP should be valid digits."
			}
		}
		if(errMsg != null){
			swal({
				title: errMsg,
				icon: 'error',
				showConfirmButton: false,
				timer: 10000
			});
			return false;
		}
		if(student_id !=''){
			$('.btn_disable').prop('disabled', true);
			showLoading(); 
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				url: config.routes._verify_only_mobile_student_otp,
				type: "post",
				dataType: "json",
				data: {'otp':otp,'student_id':student_id},
				success: function (response) {
					if(response == true){
						swal({
							title: 'OTP matched.',
							text: "Your OTP has been successfully matched.Complete your more details.",
							icon: 'success',
							showConfirmButton: true,
							timer: 3000
						}).then(() => {
							location.reload();
						});
					}else{
						$(".otpinput").val("");
						$(".btn_disable").removeAttr("disabled");
						swal({
							title: 'Invalid OTP.Please enter valid OTP',
							icon: 'error',
							showConfirmButton: false,
							timer: 10000
						});
						return false;
					}     
				}
			});	
		}
	});

	var div_timer;
	var myTimer;
	begin();
	function begin() {
		div_timer = 120;
		var link = $('.disabledCustom').attr('data-link');
		$('#div_timer').html(div_timer);
		myTimer = setInterval(function() {
		--div_timer;
		$('#div_timer').html(div_timer);
		if (div_timer === 0) {
			clearInterval(myTimer);
			$('#div_timer').html('');
			$('.disabledCustom').attr('href', link);
		}
		}, 1000);
	}

	
	


	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxPersonalDetilasValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$('.uppercase').each(function(index, element) {
					var ov = $(this).val();
					var nv = $(this).val().toUpperCase();
					$(this).val(nv);
				});
				$.ajax({
					url: config.routes.ajaxPersonalDetilasValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response.isValid==false){
							message = JSON.stringify(response); 
							// var obj = JSON.parse(response);
							 
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
								//text: "You won't be able to revert this!",
								text: form_edit_msg,
								icon: 'success',
								buttons: true,
							 })
							.then((willsave) => {
								if (willsave) { 
									$('.btn_disable').prop('disabled', true);
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

$(function(){
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "getmonthlabel";
	var ajaxUrl = ajaxBaseUrl + action;
	$('#stream').on('change', function(){
		var value = $(this).val();
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.getmonthlabel,
			data: { value: value},
			success: function(label) {
				//$.each(label, function(key, value) {
				//$('.adm_type').append("<option value='" + label.admin_id + "'>" + label.fname + "</option>");
				//});
				$("#first_name01").val(label);
			}
		});
		
	});
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxPersonaladmtype";
	var ajaxUrl = ajaxBaseUrl + action;
	$('.streams').on('change', function(){
	var stream = $(".stream").val();
	if(stream!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.ajaxPersonaladmtype,
			data: { stream:stream },
			success: function(data) { 
			 $("#admtype").html('<option value="">Select प्रवेश प्रकार(Admission Type)</option>');
				$.each(data,function(key,value){
					$('#admtype').append('<option value="' + key + '">' +value+'</option>');
					$("#admtype").trigger('contentChanged');
				});
			}
		});
	}
});

var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxPersonalborad";
	var ajaxUrl = ajaxBaseUrl + action;
	$('.admtypes').on('change', function(){
	/* change on 25-06-2024 as on change in personal page borad was notshowing */
	//var admtype = $(".admtype").val();
	var admtype = $(this).val();
	if(admtype!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.ajaxPersonalborad,
			data: { admtype:admtype },
			success: function(data) { 
			   $("#board").html('<option value="">Select बोर्ड (board)</option>');
				$.each(data,function(key,value){
					$('#board').append('<option value="' + key + '">' +value+'</option>');
					$("#board").trigger('contentChanged');
				});
			}
		});
	}
});

var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
var action = "previousqualificationget";
var ajaxUrl = ajaxBaseUrl + action;

$('.allstreamcourseadmtype').on('change', function(){
	var course = $(".course").val();
	var adm_type = $(".adm_type").val();
	var stream = $(".stream").val();
	
	if(course!='' && adm_type!='' && stream!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.previousqualificationget,
			data: { course: course,adm_type: adm_type,stream:stream },
			success: function(data) {
               $("#pre_qualification").html('<option value="">Select पिछली योग्यता (Previous Qualification)</option>');
				$.each(data,function(key,value){
					$('#pre_qualification').append('<option value="' + key + '">' +value+'</option>');
					$("#pre_qualification").trigger('contentChanged');
				});				
			}
		});
	}
});

$(".sso_id").change(function(){
        if($(this).val()!=''){
            showLoading();
            $.ajax({
            url: config.routes.ajaxGetSSOIDDetials,
            type: "get",
            dataType: "json",
            data: {'sso_id':$(this).val()},
            success: function (response) {
                if(response!=null){
                    //sso details show in confim box start
						var ssoDetails = "";
						if(typeof(response.SSOID) != "undefined" && response.SSOID !== null) {
							ssoDetails += "SSO : " + response.SSOID + ", ";
						}
						if(typeof(response.firstName) != "undefined" && response.firstName !== null) {
							ssoDetails += "Name : " + response.firstName + " ";
						}
						if(typeof(response.lastName) != "undefined" && response.lastName !== null) {
							ssoDetails += response.lastName + ", ";
						}
						if(typeof(response.gender) != "undefined" && response.gender !== null) {
							ssoDetails += "Gender : " + response.gender + ", ";
						}
						if(typeof(response.mobile) != "undefined" && response.mobile !== null) {
							ssoDetails += "Mobile : " + response.mobile + ".";
						}
						swal({
							title: 'Are you sure with ' + ssoDetails,
							text: "SSO Details",
							icon: 'success',
							buttons: true,
						})
						.then((willsave) => {
							if (willsave) { 
								
							}else{
								$('.ssoinput').val('');
							}
						});
						hideLoading();
						return false;
					//sso details show in confim box end
                    hideLoading();
                    return false;
                }else{
					$('.ssoinput').val('');
                    swal({
                        title: 'Invalid SSO',
                        text: "Please enter valid SSO.",
                        icon: 'error',
                         buttons: {OK: true},
						})
						.then((willsave) => {
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


$('#course').change(function(){
 var value = $(this).val();
  if (value == "12"){
	$("#source_other").show();
	$("#source_other1").show();
	 $('#my_date_picker').datepicker({
		defaultDate: new Date('07/01/2009'),
		maxDate: new Date('07/01/2009')
		});
	}else if (value == "10"){
	$("#source_other").hide();
	$("#source_other1").hide();
	$('#my_date_picker').datepicker({
		defaultDate: new Date('07/01/2010'),
		maxDate: new Date('07/01/2010')
		});
	} 
});


$('#disability').change(function(){
 var value = $(this).val();
  if (value != "10"){
	$("#source_others_disability").show();
	}else{
	$("#source_others_disability").hide();
	}

});

if (course == "12"){
	 // Date Object
		 $('#my_date_picker').datepicker({
		defaultDate: new Date('07/01/2009'),
		maxDate: new Date('07/01/2009')
		});
}else if(course == "10")		
  	$('#my_date_picker').datepicker({
		defaultDate: new Date('07/01/2010'),
		maxDate: new Date('07/01/2010')
	});
});

$('.gendercls').on('change', function(){
	var gender_id = $(".gendercls").val();  
	if(gender_id != ''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.getdisadvantagegroup,
			data: { gender_id: gender_id },
			success: function(data) {
               $(".dis_adv_group_cls").html('<option value="">Select वंचित वर्ग (Disadvantage Group)</option>');
				$.each(data,function(key,value){
					$('.dis_adv_group_cls').append('<option value="' + key + '">' +value+'</option>');
					$(".dis_adv_group_cls").trigger('contentChanged');
				});				
			}
		});
	}
});