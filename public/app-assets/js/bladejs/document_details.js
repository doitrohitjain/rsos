$("input[type=text]").blur(function () { //
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
  
$('.show_confirm').click(function(event) {
	event.preventDefault();
	const url = $(this).attr('href');
	swal({
		title: 'Are you sure save your information.',
		text: "You won't be able to revert this!",
		icon: 'success',
		buttons: true,
	})
	.then(function(value) {
        if (value) {
			$('.show_confirm').attr("disabled","disabled");
			window.location.href = url;
        }
    }); 
});
	   
	   
	