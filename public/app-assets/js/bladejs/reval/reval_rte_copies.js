$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#" + config.data.formId;
	var action = "ajaxRevalRTISubmission";
	var ajaxUrl = ajaxBaseUrl + action; 
	 
}); 
$('.num').on('keypress', function(e){
	// Get the key code of the pressed key
	var keyCode = e.which ? e.which : e.keyCode;
	// Allow only numeric keys (0-9) and special keys like backspace (8), delete (46), and arrow keys (37-40)
	if ((keyCode < 48 || keyCode > 57) && keyCode !== 8 && keyCode !== 46 && (keyCode < 37 || keyCode > 40)) {
		e.preventDefault();
	}
}); 
$(document).on("change",".revalRteStatusCls",function() {
	var formInput = [];
	showLoading();
	var thisVal = $(this).val();
	var thisName = $(this).attr('name');
	var thisItem = $(this).attr('item');
	var thisId = $(this).attr('id');
	var thisKey = $(this).attr('key');
	var samId = $('#student_allotment_marks_id_' + thisKey).val();
	var revalStSubId = $('#reval_student_subjects_id_' + thisKey).val();

	formInput = {'item' : thisItem,'val' : thisVal,"key":samId,"revalStSubId":revalStSubId};
	
	$.ajax({
		url: config.routes.ajaxRevalRTISubmission,
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		data: formInput,
		success: function (response) {
			hideLoading();
			var spanId = "#span_" + thisItem +"_id_"+thisKey;
			var color = "green";
			if(response.status == false){
				$("#"+thisId).val("");
				var message = response.error;
				color = 'red';
				spanMsg(spanId,'<span style="font-size:12px;">Not Updated!</span>',color);
				setTimeout(
					function(){
						spanMsg(spanId,'',color);
					}
				, 3000);
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
				$("#reval_rte_status"+thisKey).html(response.data.reval_rte_status);
				spanMsg(spanId,response.data.msg,color);
				setTimeout(
					function(){
						spanMsg(spanId,'',color);
					}
				, 3000);
			} 
		},
		error: function (data) {
			console.log('Error:', data); 
			hideLoading();
		}
	}); 

});
function spanMsg(spanId,msg,color){
	$(spanId).html("<span style='color:" + color +" !important;'>" + msg + "</span>");
	return true;
}
	
	
	
	
	
	
	
	
	
	
	
	




