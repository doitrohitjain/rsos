$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "updateStudentDetailsPrintValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "updatedetailsform";
	$(formId).validate({
		rules: {
			name:{
				required: true,
				maxlength:100,
			},
			father_name:{
				required: true,
				maxlength:100,
			},
			mother_name:{
				required: true,
				
			},
			dob:{
				required: true,
			},
			mobile:{
				required: true,
				maxlength:10,
			},
		},
		messages: {
			name:{
				required: "Name is Required.",
			},
			father_name:{
				required: "Father Name is Required.",
			},
			mother_name:{
				required: "Mother Name is Required.",
			},
			dob:{
				required: "Date of birth Name is Required.",
			},
			mobile:{
				required: "Mobile Number is Required.",
			},
			
		},
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				
				showLoading(); 
				$.ajax({
					url: config.routes.updateStudentDetailsPrintValidation,
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
$("input[type=text]").blur(function () { 
	$(this).val($(this).val().toUpperCase());
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
	if(error == null){
		if((sizeInKb < minImageSize) || (sizeInKb > imageSize)){
			$(this).val('');
			error = 'Please select valid file min size (' + minImageSize +  ') kb and max size (' + imageSize +  ') kb and .';
		}  
		if((sizeInKb < minImageSize) || (sizeInKb > imageSize)){ 
			$(this).val('');
			error = 'Please select valid file min size (' + minImageSize +  ') kb and max size (' + imageSize +  ') kb and .';
		} 
	}
	// console.log(type + " " +sizeInKb + " - " + imageSize + " - " + minImageSize + " - "  );
	//d 24.4248046875 - 500 - 50 -
	// console.log(error );
	// return  false;
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
	
	/* New Code for client side valdiaiton end */ 
	return false;

	if(type == 'i'){
		imageSize = 5000; //kb
		_validImageExtensions = "jpg|jpeg|bmp|gif|png";
	}else if(type == 'd'){
		imageSize = 7000; //kb
		_validImageExtensions = "pdf|jpg|jpeg|bmp|gif|png";
	}

	var val = $(this).val().toLowerCase();
	regex = new RegExp("(.*?)\.(" + _validImageExtensions +")$");
	var error = null;
	if (!(regex.test(val))) {
		$(this).val('');
		error = 'Please select correct file format. Allowed formats ( ' + _validImageExtensions + ".";
	}

	if(sizeInKb > imageSize){
		$(this).val('');
		error = 'Please select valid file size.';
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
	function showLoading() { 
		$('.mainCls').css('display', 'block');
	  } 
	function hideLoading() {
		$('.mainCls').css('display', 'none');
	}
	$('.course_id').on('change',function(){
		var course = this.value;
		$(".subjectsid").html('');
		$.ajax({
			url: config.routes.getsubjects,
			type: "get",
			data: {'id': course},
			dataType : 'json',
			success: function (result){
				$(".subjectsid").html('<option value="">Select subjects</option>');
				$.each(result,function(key,value){
					$('.subjectsid').append('<option value="' + key + '">' +value+'</option>');
					$(".subjectsid").trigger('contentChanged');
				});	
			},
		});
	});
	
});

$(function(){
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
