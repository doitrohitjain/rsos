// </script>

    // jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + config.data.formId;
		$(formId).validate({
			rules: {
				'locksumbitted': {required: true},
				'Declaration': {required: true},
				},
			messages: {
				'locksumbitted': {required: "lock Sumbitted is required"},
				'Declaration': {required: "Declaration is required"},
			},
				errorPlacement: function (error, element) {
				if (element.attr("name") == "locksumbitted"){
					swal({
						title: 'कृपया नियम और शर्तों को स्वीकार करने के लिए चेक बॉक्स का चयन करें(Please select the check box to accept the terms & conditions)',
						text: 'Something went wrong!',
					icon: 'error',
				})
				}else if(element.attr("name") == "Declaration"){
					swal({
						title: 'कृपया नियम और शर्तों को स्वीकार करने के लिए चेक बॉक्स का चयन करें(Please select the check box to accept the terms & conditions)',
						text: 'Something went wrong!',
					icon: 'error',
				})
				}
				},
				submitHandler: function (form) {
				var clsName = "api";
		
				if($(formId).hasClass(clsName)){
						
						return true;
				}else{
					event.preventDefault();
					swal({
						title: 'Are you sure save your information.',
						text: "You won't be able to revert this!",
						icon: 'success',
						buttons: true,
					})
					.then((willsave) => {
						if (willsave) {
						
							$(formId).addClass(clsName); 
							$(formId).submit();
						}
					});
				}
			}
		});
	});
	

$('.aicenter_verify').on('click', function (event) {
	
	event.preventDefault();
	const url = $(this).attr('data-url');
	msg = "Are you sure you want to Revert Ai centar Verification ?";  
	swal({
		title: 'Are you sure?',
		text: msg,
		icon: 'info',
		buttons: ["Cancel", "Yes!"],
	}).then(function(value) {
		if (value) {
			window.location.href = url;
		}
	});
});


$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be Active!',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
			showLoading();
            window.location.href = url;
        }
    });
});


$('.department_verify').on('click', function (event) {
	
	event.preventDefault();
	const url = $(this).attr('data-url');

	msg = "Are you sure you want to Revert Department Verification ?";  
	swal({
		title: 'Are you sure?',
		text: msg,
		icon: 'warning',
		confirmButtonColor: '#8CD4F5',
		buttons: ["Cancel", "Yes!"],
	}).then(function(value) {
		if (value) {
			window.location.href = url;
		}
	});
});



	
// </script>







