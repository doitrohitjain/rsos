$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxCorrectionMarksheetValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "correctiondata";
	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxCorrectionMarksheetValidation,
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



$("body").on("change",".inputfile",function(e){ 

	e.preventDefault();
	var formId = "#" + $(this).attr("data-formId");
	var type = $(this).attr("data-type");
	 
	var sizeInKb = this.files[0].size / 1024;
	var imageSize = null;
	var _validImageExtensions = null;
	 

	/* New Code for client side valdiaiton start */ 
	if(type == 'i'){
		minImageSize = 50;
		imageSize = 100; //kb
		_validImageExtensions = "jpg|jpeg|bmp|gif|png|pdf";
	}else if(type == 'd'){
		minImageSize = 50;
		imageSize = 500; //kb
		_validImageExtensions = "pdf|jpg|jpeg|bmp|gif|png";
	}

	var val = $(this).val().toLowerCase();
	regex = new RegExp("(.*?)\.(" + _validImageExtensions +")$");
	var error = null;
	if (!(regex.test(val))) {
		
		error = 'Please select correct file format. Allowed formats ( ' + _validImageExtensions + ".";
	} 
	if(error == null){
		if((sizeInKb < minImageSize) || (sizeInKb > imageSize)){
			
			error = 'Please select valid file min size (' + minImageSize +  ') kb and max size (' + imageSize +  ') kb and .';
		}  
		if((sizeInKb < minImageSize) || (sizeInKb > imageSize)){ 
			
			error = 'Please select valid file min size (' + minImageSize +  ') kb and max size (' + imageSize +  ') kb and .';
		} 
	}
	// console.log(type + " " +sizeInKb + " - " + imageSize + " - " + minImageSize + " - "  );
	//d 24.4248046875 - 500 - 50 -
	// console.log(error );
	// return  false;
	if(error == null){
		$("#support_document").val('1');
	}else{ 
		swal({
            title: "Validation Error",
            text: error,
            icon: "error",
            button: "Close",
            timer: 10000
        });
$(this).val('');	
}

}); 






$(document).ready(function () {
	$(".document").change(function () {
	   if($("#marksheet_type").val() != "" && $("#document_type").val() != ""){
		   $('.revised').show();
			if($("#marksheet_type").val() == 2){
				$('.duplicate').hide();
			}else{
				$('.duplicate').show();
			}
		   
	   }else{
		 $('.revised').hide();  
	   }
	});
});