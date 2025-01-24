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
							var extra = "";
							lowerText = " You won't be able to revert this! ";
							
							extra = response.extra;  
							if(extra !== "" || extra !== null || extra !== 'null'){
								// extra = "";
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
		$(mainLblId).html('<span class="chip lighten-5 green green-text">Accept</span>');
	}else{
		$(mainLblId).html('<span class="chip lighten-5 red red-text">Objection</span>');
	}
	return true;
});

$('.cls_fresh_student_doc_update_status').on('click', function () {	
	var item = $(this).attr("name");
	var id = $(this).attr('id');
	var item_name = $(this).attr("data-item_name");
	var kConter = $(this).attr("data-kConter");
	var main_item_name = $(this).attr("data-main_item_name");
	var val = $(this).val();
	var otherIdItem = null;
	var remarks  = ".cls_" + main_item_name + "_remarks_div";
	var remarksInput  = ".cls_" + main_item_name + "_remarks";
	var showStatus = false; 
	var isWorking = false;
	M.Toast.dismissAll();
	 
	if(item_name == "Approved"){
		if ($("input[id="+id+"]").is(":checked")){
			isWorking=true;
			otherIdItem = "#Rejected_"+ main_item_name;
			$(otherIdItem).prop('checked', false);
			$("#"+main_item_name+"_approved").removeClass("hide");
			$("#"+main_item_name+"_rejected").addClass("hide");
			$("#"+main_item_name+"_pending").addClass("hide");
			$('.collapsible').collapsible('close', kConter);
			var temp = parseInt(kConter) + 1;temp = parseInt(temp);
			$('.collapsible').collapsible('open', temp);
			M.toast({html:  '' + item_name + '', classes: 'rounded green'}); 
		}
	}else if(item_name == "Rejected"){
		if ($("input[id="+id+"]").is(":checked")){
			isWorking=true;
			otherIdItem = "#Approved_"+ main_item_name;
			$("#"+main_item_name+"_approved").addClass("hide");
			$("#"+main_item_name+"_rejected").removeClass("hide");
			$("#"+main_item_name+"_pending").addClass("hide");
			$(otherIdItem).prop('checked', false);
			showStatus = true;
		}
	}  
	if(isWorking != true){
		$("#"+main_item_name+"_approved").addClass("hide");
		$("#"+main_item_name+"_rejected").addClass("hide");
		$("#"+main_item_name+"_pending").removeClass("hide");
	}
	if(showStatus){
		$(remarks).removeClass('hide'); 
	}else{
		$(remarksInput).val("");
		$(remarks).addClass('hide'); 
	}
});