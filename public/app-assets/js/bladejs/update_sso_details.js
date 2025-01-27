$(document).ready(function() {
	 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxUpdateSsoDetilasValidations";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "updatessoform";
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxUpdateSsoDetilasValidations,
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

$(function(){
	$('.ssocheck').attr("disabled",true);
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "getmonthlabel";
	var ajaxUrl = ajaxBaseUrl + action;
$(".sso_id").change(function(){
        if($(this).val()!=''){
            showLoading();
            $.ajax({
            url: config.routes.ajaxGetSSOIDDetials,
            type: "get",
            dataType: "json",
            data: {'sso_id':$(this).val()},
            success: function (response) {
				$('.ssocheck').attr("disabled",false);
                if(response != null){
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
                }else{
					$('.ssoinput').val('');
					$('.ssocheck').attr("disabled",true);
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
 });

