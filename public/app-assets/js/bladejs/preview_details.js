// </script>

    // jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + config.data.formId;
		$(".btn_disabled").removeAttr("disabled");
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
					title: 'Please select check box',
					text: 'Something went wrong!',
					icon: 'error',
				})
				}else if(element.attr("name") == "Declaration"){
					swal({
					title: 'Please select check box',
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
							$('.btn_disabled').prop('disabled', true);
							$(formId).addClass(clsName); 
							$(formId).submit();
						}
					});
				}
			}
		});
	});
	
// </script>







