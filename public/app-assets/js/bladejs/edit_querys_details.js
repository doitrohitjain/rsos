$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxqueryValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val() == 1){
				showLoading();
				$.ajax({
					url: config.routes.ajaxqueryValidation,
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
	var indiceDatos = $('#is_sql')[0].selectedIndex;
	if (indiceDatos == '1') {
	$("div").find("[did='sql']").show();
	$("div").find("[did='url']").hide();	
	}if (indiceDatos == '2') {
	$("div").find("[did='sql']").hide();
	$("div").find("[did='url']").show();	
	}
	$('#is_sql').change(function(){
		var value = $(this).val();
		if (value == '1') {
			$("div").find("[did='is_pdf']").show();
			$("div").find("[did='is_excel']").show();
			$("div").find("[did='sql']").show();
			$("div").find("[did='url']").hide();
		}else if (value == '2') {
			$("div").find("[did='is_pdf']").hide();
			$("div").find("[did='is_excel']").hide();
			$("div").find("[did='sql']").hide();
			$("div").find("[did='url']").show();
		}
	});
});
