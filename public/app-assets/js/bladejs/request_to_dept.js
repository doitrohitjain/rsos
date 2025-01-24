$(document).ready(function(){  
	$(".btn_disabled").removeAttr("disabled");
	var errStr = "Please enter remarks.";
	var formId = "#Student";
	$(formId).on('submit',function(){
		var request_to_dept_remarks  = $("#request_to_dept_remarks").val();
		if(request_to_dept_remarks == ""){
			swal({
				title: "Validation Error",
				text: errStr,
				icon: "error",
				button: "Close",
				timer: 30000
			});
			return false;
		}else{  
			var clsName = "api";
			if($(formId).hasClass(clsName)){ 
				return true;
			}
			swal({
				title: 'Are you sure save your information.',
				text: "You won't be able to revert this!",
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
	});

	
});  