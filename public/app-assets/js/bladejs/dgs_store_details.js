$(document).ready(function() {
	 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxDgsAddStudensValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "CreateDgsStudents";
	
	
	$(formId).validate({
		//rules: {
			//ssoid: { required: true , minlength: 2, maxlength: 50 },
			//name: { required: true , minlength: 2, maxlength: 50 },
			//email : { required: true , email: true},
			//mobile : { required: true , number:true  ,minlength: 10,maxlength: 12},
			//designation:{required: true}
		//},
		//messages: {
			//ssoid: { 
				//required: "SSO ID is required",
				//minlength: "SSO ID must be 2 min charector",

				//maxlength: "SSO ID must be less than 50 charector"
			//},
			//name: { 
				//required: "Name is required",
				//minlength: "Full Name must be 2 min charector",
				//maxlength: "Full Name must be less than 50 charector"
			//},
			//mobile: { 
				//required: "Mobile is required", 
				//minlength: "Mobile must be 10 min charector",
				//maxlength: "Mobile must be less than 50 charector",
				//number: "Mobile must be numeric"
			//},
			//designation: { 
				//required: "Designation is required", 
				
			//},
		//},
		success: function(response){
			
		},
		submitHandler: function(form) {
			
			
			if($('#ajaxRequest').val() == 1){ 
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxDgsAddStudensValidation,
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
								//alert(elementt);
								//	$.each(elementt, function(indexiDis, elementtiDis) { 
										errStr += "" + counter + " : " + elementt  + "\n";
										counter++;
									//});
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
								title: 'Are you sure you want to save your information.',
								text: "You won't be able to revert this!",
								
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
	$('.my_date_picker').datepicker({
		defaultDate: new Date('07/01/2010'),
		maxDate: new Date('07/01/2010')
	});
});



