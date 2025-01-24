// <script type="text/javascript">
	// jQuery Form Validator
	
	$(document).ready(function() { 
		$('.checkBrowserIssue').on('change', function(){
			$('#state_id').find('option').attr('selectedIndex', '-1'); 
			var toAppend = '';
			toAppend += '<option>चयन राज्य (State) </option>'; 
			$('#state_id')
				.find('option')
				.remove()
				.end()
				.append(toAppend); 
			$("#state_id").trigger('contentChanged');

			$.ajax({
				url: config.routes.get_states,
				type: "get",
				dataType: "json",
				data: {'test':"test"},
				success: function (response) {
					$.each(response, function(key,value) {
						$('#state_id').append('<option value="' + key + '">' + value + '</option>');
						$("#state_id").trigger('contentChanged');
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
		
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		formId = "#" + config.data.formId;
		var action = "checkAddressValidation";
		var ajaxUrl = ajaxBaseUrl + action;
		$(formId).validate({
			/*
			rules: {
				address1: { required: true,minlength: 2,maxlength: 70},
				state_id: { required: true , number: true},
				district_id: { required: true , number: true},
				tehsil_id: { required: true , number: true},
				block_id: { required: true , number: true},
				city_name: { required: true,minlength: 2,maxlength: 30},
				pincode: { required: true,minlength: 6,maxlength: 8,number: true},
			},
			messages: {
				address1: { 
					required: "Address1 is required", 
					minlength: "Address1 must be of 2 charector",
					maxlength: "Address1 cannot be more than 70 charector",
				},
				state_id: {
					required: "State is required", 
					number: "State must be numeric"
				},
				district_id: {
					required: "District is required", 
					number: "District must be numeric"
				},
				tehsil_id: {
					required: "Tehsil is required", 
					number: "Tehsil must be numeric"
				},
				block_id: {
					required: "Block is required", 
					number: "Block must be numeric"
				},
				city_name: { 
					required: "City Name is required", 
					minlength: "City Name must be of 2 charector",
					maxlength: "City Name cannot be more than 30 charector",
				},
				pincode: { 
					required: "Pincode is required", 
					minlength: "Pincode must be of 6 digits",
					maxlength: "Pincode cannot be more than 8 digits",
					number: "Pincode must be numeric"
				} 
			},
			*/
			success: function(response){
				
			},
			submitHandler: function(form) {
				
				if($('#ajaxRequest').val()==1){
					showLoading(); 
					$.ajax({
						url: config.routes.checkAddressValidation,
						type: "POST",
						dataType: "json",
						data: $(formId).serialize(),
						success: function (response) {
							hideLoading();
							var response = response[0];
							if(response.status==false){
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
									// text: form_edit_msg,
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
				
				/*
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
				*/
			}
		});
	}); 
	
	
	$(".course").on('change',function() {
		showLoading();
		$(".examcenter_detail_id").html('<option value="">Select Exam Center</option>');
		$(".subject_id").html('<option value="">Select Subject</option>');
		
		$.ajax({
			url: config.routes.ajaxCourseExamcenters,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".examcenter_detail_id").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});

		$.ajax({
			url: config.routes.ajaxCoursesubjects,
			type: "get",
			dataType: "json",
			data: {'course':$(this).val()},
			success: function(result){ 
				hideLoading();
				$.each(result, function(key,value) {
					$(".subject_id").append($('<option>', { value : key }).text(value)); 
				});		 		 
								 
			}
		});
	});


	// jQuery Form Validator
	$('.state_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();

		$('.district_id').html('<option value="">Select District</option>');
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.block_id').html('<option value="">Select Block</option>');
		$('.tehsil_name').val('');
		$('.block_name').val('');
		
		if(id=='6'){ // 6 = 'rajasthan'
			$('.tehsil_id_section').show();
			$('.block_id_section').show();
			$('.tehsil_name_section').hide();
			$('.block_name_section').hide();
		} else {
			$('.tehsil_name_section').show();
			$('.block_name_section').show();
			$('.tehsil_id_section').hide();
			$('.block_id_section').hide();
		}
		
		if(id!=''){ 
			showLoading();
			// initialize
			$('.district_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.district_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
		
			event.preventDefault();
			$.ajax({
				url: config.routes.district_by_state_id,
				type: "get",
				data: {'id': id},
				success: function (data) {  
					$.each(data, function(key,value) {
						$('.district_id').append('<option value="' + key + '">' + value + '</option>');
						$(".district_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	
	$('.district_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();
		
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.block_id').html('<option value="">Select Block</option>');
		$('.tehsil_name').val('');
		$('.block_name').val('');
		
		if(id!=''){
			showLoading();
			// initialize
			$('.tehsil_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.tehsil_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
			
			$('.block_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
		
			event.preventDefault();
			$.ajax({
				// url: "{{ route('get_tehsil_by_district_id') }}",
				url: config.routes.get_tehsil_by_district_id,
				type: "get",
				data: {'id': id},
				success: function (data) { 
					$.each(data, function(key,value) {
						$('.tehsil_id').append('<option value="' + key + '">' + value + '</option>');
						$(".tehsil_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
			
			$.ajax({
				// url: "{{ route('get_tehsil_by_district_id') }}",
				url: config.routes.get_block_by_district_id,
				type: "get",
				data: {'id': id},
				success: function (data) { 
					$.each(data, function(key,value) {
						$('.block_id').append('<option value="' + key + '">' + value + '</option>');
						$(".block_id").trigger('contentChanged');
					});	
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
// </script>



