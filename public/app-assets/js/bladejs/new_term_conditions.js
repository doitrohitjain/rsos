// </script>

    // jQuery Form Validator
	 $(document).ready(function() {
		formId = "#" + "new_term_condition";
		$(formId).validate({
			rules: {
				'locksumbitted': {required: true},
				},
			messages: {
				'locksumbitted': {required: "lock Sumbitted is required"},
			},
				errorPlacement: function (error, element) {
				if (element.attr("name") == "locksumbitted"){
					swal({
					title: 'Please accept declaration.',
					text: 'Please first accept declaration.',
					icon: 'error',
				})
				}else if(element.attr("name") == "Declaration"){
					swal({
					title: 'Please accept declaration.',
					text: 'Please first accept declaration.',
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
						title: 'Would you like to continue?',
						text: "Would you like to accept with terms & conditions?(क्या आप नियम एवं शर्तों के साथ स्वीकार करना चाहेंगे?)",
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
	
// </script>







