$("body").on("change",".inputfile",function(e){ 

	e.preventDefault();
	var formId = "#" + $(this).attr("data-formId");
	var type = $(this).attr("data-type");
	$(".btn_disabled").removeAttr("disabled");
	var sizeInKb = this.files[0].size / 1024;
	var minImageSize = 10;
	var imageSize = null;
	var _validImageExtensions = null; 
	
	// $(formId).submit(); 
	// return true; 
	
	if(type == 'i'){
		minImageSize = 10;
		imageSize = 50; //kb
		_validImageExtensions = "jpg|jpeg|bmp|gif|png";
	}else if(type == 'd'){
		minImageSize = 50;
		imageSize = 500; //kb
		_validImageExtensions = "pdf|jpg|jpeg|bmp|gif|png";
	}

	var val = $(this).val().toLowerCase();
	regex = new RegExp("(.*?)\.(" + _validImageExtensions +")$");
	var error = null;
	if (!(regex.test(val))) {
		$(this).val('');
		error = 'Please select correct file format. Allowed formats ( ' + _validImageExtensions + ".";
	}

	// console.log(type + " " + sizeInKb + " " + imageSize + " " + minImageSize);return false;

	if(sizeInKb > imageSize){
		$(this).val('');
		error = 'Please select valid file max size (' + imageSize +  ') kb.';
	} 
	if(minImageSize > sizeInKb){
		$(this).val('');
		error = 'Please select valid file min size (' + minImageSize +  ') kb.';
	} 
	
	if(error == null){ 
		$(formId).submit(); 
	}else{ 
		swal({
            title: "Validation Error",
            text: error,
            icon: "error",
            button: "Close",
            timer: 10000
        });

		return false;
	}
	
}); 
$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxrejecteddocumentDetilasValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxrejecteddocumentDetilasValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
							hideLoading();
							if(response.status == false){
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
										$(".show_confirm").attr('disabled', 'disabled');
										$(formId).submit();
									}else{
										$('.show_confirm').removeAttr('disabled');
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


	   
	   
	