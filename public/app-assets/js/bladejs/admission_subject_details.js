// <script type="text/javascript">
	// jQuery Form Validator 
	$(document).ready(function() {
		$("input[type=text]").blur(function () { //
			$(this).val($(this).val().toUpperCase());
		});
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		$("#is_multiple_faculty").val(0);
		$(".show_confirm").removeAttr("disabled");
		var action = "ajaxSubjectValidation";
		var ajaxUrl = ajaxBaseUrl + action; 
		

		$('#faculty_type_id').on('change', function(){
			$('.subjectselect').find('option').attr('selectedIndex', '-1');
			$(".subjectselect").trigger('contentChanged');

			var toAppend = '';
			toAppend += '<option>Select Any </option>'; 
			$('.subjectselect')
				.find('option')
				.remove()
				.end()
				.append(toAppend); 
			$('.subjectselect').find('option').attr('selectedIndex', '-1');
			$(".subjectselect").trigger('contentChanged');
			var selected = $("#faculty_type_id").val();
			$.ajax({
				url: config.routes.get_subject_faculty_wise,
				type: "get",
				dataType: "json",
				data: {selected:selected},
				success: function (response) {
					$.each(response, function(key,value) {
						$('.subjectselect').append('<option value="' + key + '">' + value + '</option>');
						$(".subjectselect").trigger('contentChanged');
					});	
					hideLoading();
					return false;
				},
				error: function (response) {
					console.log('Error:', response); 
					hideLoading();
					return false;
				}
			});
		});


		$(formId).validate({
			success: function(response){
			},
			submitHandler: function(form) {
				var book_learning_type_id = $("#book_learning_type_id").val();
				var faculty_type_id = $("#faculty_type_id").val();
				if(faculty_type_id == ""){
					/* var message = "Please select Preferred Faculty.(कृपया पसंदीदा मुख्य संकाय का चयन करें.) ";
					swal({
						title: "Validation Error",
						text: message,
						icon: "error",
						button: "Close",
						timer: 30000
					});
					return false;*/
				}
				
				if(book_learning_type_id == ""){
					var message = "Please select types of learning e-content/books.(कृपया सीखने की ई-सामग्री/पुस्तकों के प्रकार चुनें.) ";
					swal({
						title: "Validation Error",
						text: message,
						icon: "error",
						button: "Close",
						timer: 30000
					});
					return false;
				}
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.ajaxSubjectValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							if(response != 1){
								var message = "";
								$.each(response, function() {
									$.each(this, function(k, v) {
										message += v;
									});
								});
								swal({
									title: "Validation Error",
									text: message,
									icon: "error",
									button: "Close",
									timer: 30000
								});
								return false;
							} else {
								var faculty_type_id = $("#faculty_type_id").val();
								var selectedFacultyName = "";
								var selectedHindiFacultyName = "";
								if(faculty_type_id == 1){
									selectedFacultyName = "Science";
									selectedHindiFacultyName = " विज्ञान ";
								}else if(faculty_type_id == 2){
									selectedFacultyName = "Commerce";
									selectedHindiFacultyName = " वाणिज्य  ";
								}else if(faculty_type_id == 3){
									selectedFacultyName = "Arts";
									selectedHindiFacultyName = " आर्ट्स ";
								}else if(faculty_type_id == 4){
									selectedFacultyName = "Agriculture";
									selectedHindiFacultyName = " कृषि  ";
								}
								
								//selectedFacultyName = "You are selected " + selectedFacultyName + " preferred faculty but other faculties subjects are selected. Would you like to save and continue with this?(आप " + selectedHindiFacultyName + " पसंदीदा संकाय चयनित हैं लेकिन अन्य संकाय विषय चयनित हैं। क्या आप इसे सहेजना और जारी रखना चाहेंगे?)";	


								selectedFacultyName = "You are selected " + selectedFacultyName + " preferred faculty but other faculties subjects are selected.(आप " + selectedHindiFacultyName + " पसंदीदा संकाय चयनित हैं लेकिन अन्य संकाय विषय चयनित हैं।)";									
								$.ajax({
									url: config.routes.ajaxFacultySubjectValidation,
									type: "POST",
									dataType: "html",
									data: $(formId).serialize(),
									success: function (again_response) {
										hideLoading();
										$("#is_multiple_faculty").val(1);
										if(again_response == 2){
											swal({
												title: "Validation Error",
												text: selectedFacultyName,
												icon: "error",
												button: "Close",
												timer: 30000
											});
											return false;
											/*swal({
												title: selectedFacultyName,
												text: "You won't be able to revert this!",
												icon: 'success',
												buttons: true,
											})
											.then((willsave) => {
												if (willsave) {
													var clsName = "api";
													if($(formId).hasClass(clsName)){
														return true;
													}
													swal({
														title: 'Would you like to save your information?(क्या आप अपनी जानकारी सहेजना चाहेंगे?)',
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
											});*/
										} else { 
											var clsName = "api";
											if($(formId).hasClass(clsName)){
												return true;
											}
											swal({
												title: 'Would you like to save your information?(क्या आप अपनी जानकारी सहेजना चाहेंगे?)',
												//text: "You won't be able to revert this!",
												text: form_edit_msg,
												icon: 'success',
												buttons: true,
											})
											.then((willsave) => {
												if (willsave) {
													$('.show_confirm').prop('disabled', true);
													$(formId).addClass(clsName); 
													$('#ajaxRequest').val('0');
													$(formId).submit();
												}
											});
										} 
									},
									error: function (data) {
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
	
	$('.reset').on("click",function(){
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		$('.subject_id').find("option:selected").val('');
	});
// </script>



