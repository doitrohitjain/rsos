$(document).ready(function() {
	var address1 = null;
	var address2 = null;
	var address3 = null;
	var state_id = null;
	var district_id = null;
	var block_id = null;
	var block_name = null;
	var city_name = null;
	var pincode = null;
	var tehsil_id = null;
	var tehsil_name = null;
	
	
	if($("#is_both_sameadress").is(':checked')){ 
		$(".currentInput").attr("disabled",true);
	}else{
		$(".currentInput").removeAttr("disabled");
	}
	
	$('.currentCheckBrowserIssue').on('change', function(){
		$('#state_id').find('option').attr('selectedIndex', '-1'); 
		var toAppend = '';
		toAppend += '<option>चयन राज्य (State) </option>'; 
		$('#current_state_id')
			.find('option')
			.remove()
			.end()
			.append(toAppend); 
		$("#current_state_id").trigger('contentChanged');

		$.ajax({
			url: config.routes.get_states,
			type: "get",
			dataType: "json",
			data: {'test':"test"},
			success: function (response) {
				$.each(response, function(key,value) {
					$('#current_state_id').append('<option value="' + key + '">' + value + '</option>');
					$("#current_state_id").trigger('contentChanged');
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
	
	$('#is_both_sameadress').on("click", function() {
		
		if($("#is_both_sameadress").is(':checked')){
			address1 = $("#address1").val();
			address2 = $("#address2").val();
			address3 = $("#address3").val();
			state_id = $("#state_id").find("option:selected").val();
			
			if(state_id == 6){
				tehsil_id = $("#tehsil_id").find("option:selected").val();	
				block_id = $("#block_id").find("option:selected").val();
			}else{
				tehsil_name = $("#tehsil_name").val();	
				block_name = $("#block_name").val();
			}
			
			
			
			district_id = $("#district_id").find("option:selected").val();
			
			city_name = $("#city_name").val();
			pincode = $("#pincode").val();
			var errMsg = "";
			if(address1 == "" || state_id == "" || district_id == "" || pincode == "" || city_name == "" ){
				errMsg = "Please fill the (*) mandatory fields in permanent address.";
				$("#is_both_sameadress").val();
			}
			
			if(errMsg != ""){
				swal({
					title: "Validation Error",
					text: errMsg,
					icon: "error",
					button: "Close",
					timer: 30000
				});
				return false;
			}
			
			
			
		}
		
		
		$("#current_address1").val(address1);
		$("#current_address1").trigger('contentChanged');
		$("#current_address2").val(address2);
		$("#current_address2").trigger('contentChanged');
		$("#current_address3").val(address3);
		$("#current_address3").trigger('contentChanged'); 
		$("#current_pincode").val(pincode);
		$("#current_pincode").trigger('contentChanged');
		$("#current_city_name").val(city_name);
		$("#current_city_name").trigger('contentChanged'); 
		
		
		
		
		 
		
		
		//$('.tehsil_name').val('');
		$('.current_block_name').val('');
		var id = state_id;
		if(id == '6'){ 
           // 6 = 'rajasthan'
			$('.current_tehsil_id_section').show();
			$('.current_block_id_section').show();
			$('.current_tehsil_name_section').hide();
			$('.current_block_name_section').hide();
		} else {
			$('.current_tehsil_id_section').hide();
			$('.current_tehsil_name_section').show();
			$('.current_block_name_section').show();
			$('.current_block_id_section').hide();
		}
		
		if(id != ''){   
			// initialize
			// $('.current_district_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			// $('.current_district_id').on('contentChanged', function() {
				// $(this).material_select();
				// $(this).formSelect();
			// });
		
			 
			$.ajax({
				url: config.routes.district_by_state_id,
				type: "get",
				data: {'id': id},
				success: function (data) {  
					$.each(data, function(key,value) {
						$('.current_district_id').append('<option value="' + key + '">' + value + '</option>');
						
					});	
					$("#current_district_id").val(district_id).trigger('change');
					$(".current_district_id").trigger('contentChanged');
					
					hideLoading();
				},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
		
		
		$("#current_state_id").val(state_id).trigger('change');
		$("#current_state_id").trigger('contentChanged');
		
		
		
		 
		
		
		
		 
		
		
		
		
		
		
		
		/* district selection start */ 
			$("#current_district_id").val(district_id).trigger('change');
			$("#current_district_id").trigger('contentChanged');
		/* district selection end */
		/* block and tehsil selection start */  
			if(state_id == 6){
				$(".current_tehsil_id").val(tehsil_id).trigger('change');
				$(".current_tehsil_id").trigger('contentChanged');
			}else{
				$(".current_tehsil_name").val(tehsil_name);
			}
	
		
			if(state_id == 6){
				$("#current_block_id").val(block_id).trigger('change');
				$("#current_block_id").trigger('contentChanged');
			}else{
				$(".current_block_name").val(block_name);
			} 
		/* block and tehsil selection end */
		
		if($("#is_both_sameadress").is(':checked')){
			$(".currentInput").attr("disabled",true);
		}else{ 
			$(".currentInput").removeAttr("disabled");
		}
		

	}); 
	// jQuery Form Validator
	$('.state_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();
		$('.district_id').html('<option value="">Select District</option>');
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.block_id').html('<option value="">Select Block</option>');
		//$('.tehsil_name').val('');
		$('.block_name').val('');
		
		if(id=='6'){ 
           // 6 = 'rajasthan'
			$('.tehsil_id_section').show();
			$('.block_id_section').show();
			$('.tehsil_name_section').hide();
			$('.block_name_section').hide();
		} else {
			$('.tehsil_id_section').hide();
			$('.tehsil_name_section').show();
			$('.block_name_section').show();
			$('.block_id_section').hide();
		}
		
		
	});
	
	$('.district_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();		
		$('.tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.block_id').html('<option value="">Select Block</option>');
		//$('.tehsil_name').val('');
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
	});

	$('.current_state_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();
		$('.current_district_id').html('<option value="">Select District</option>');
		$('.current_tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.current_block_id').html('<option value="">Select Block</option>');
		//$('.tehsil_name').val('');
		$('.current_block_name').val('');
		
		if(id == '6'){ 
           // 6 = 'rajasthan'
			$('.current_tehsil_id_section').show();
			$('.current_block_id_section').show();
			$('.current_tehsil_name_section').hide();
			$('.current_block_name_section').hide();
		} else {
			$('.current_tehsil_id_section').hide();
			$('.current_tehsil_name_section').show();
			$('.current_block_name_section').show();
			$('.current_block_id_section').hide();
		}
		
		if(id!=''){ 
			showLoading();
			// initialize
			$('.current_district_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.current_district_id').on('contentChanged', function() {
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
						$('.current_district_id').append('<option value="' + key + '">' + value + '</option>');
						$(".current_district_id").trigger('contentChanged');
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
	
	$('.current_district_id').on("select2:select", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();		
		$('.current_tehsil_id').html('<option value="">Select Tehsil</option>');
		$('.current_block_id').html('<option value="">Select Block</option>');
		//$('.tehsil_name').val('');
		$('.current_block_name').val('');
		
		if(id!=''){
			showLoading();
			// initialize
			$('.current_tehsil_id').formSelect();
			
			// setup listener for custom event to re-initialize on change
			$('.current_tehsil_id').on('contentChanged', function() {
				// $(this).material_select();
				$(this).formSelect();
			});
			
			$('.current_block_id').on('contentChanged', function() {
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
						$('.current_tehsil_id').append('<option value="' + key + '">' + value + '</option>');
						$(".current_tehsil_id").trigger('contentChanged');
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
						$('.current_block_id').append('<option value="' + key + '">' + value + '</option>');
						$(".current_block_id").trigger('contentChanged');
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






