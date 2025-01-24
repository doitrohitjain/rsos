// <script type="text/javascript">
	// jQuery Form Validator 
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$('.are_you_from_rajasthan').on("select2:select", function(event){
		var are_you_from_rajasthan = $(event.currentTarget).find("option:selected").val();
		$('#jan_aadhar_number').val('');
		if(are_you_from_rajasthan == 1){
			$('.jan_aadhar_number_cls').show();
		}else{
			$('.jan_aadhar_number_cls').hide();
			$('.submitBtn').removeClass('disabled');
		}
	});

	$('.stream').on("select2:select", function(event){
		var stream = $(event.currentTarget).find("option:selected").val();
		$('.adm_type').html('<option selected="selected" value="" >Select प्रवेश प्रकार (Admission Type)</option>');
		if (stream==1) {
			$('.adm_type').html('<option selected="selected" value="" >Select प्रवेश प्रकार (Admission Type)</option><option value="1">General Admission</option><option value="2">Re-admission</option><option value="3">Part Admission</option><option value="4">Improvement</option><option value="5">ITI</option>');
		} 
		if (stream==2) {
			$('.adm_type').html('<option selected="selected" value="" >Select प्रवेश प्रकार (Admission Type)</option><option value="1">General Admission</option>');
		} 
	});

	$(document).on("click","#janAadharAction",function() {
		if (!$("input[name='nameEng']:checked").val()) {
			alert('Nothing is checked!');
			return false;
			
		} else {
			
		  	$("#member_number").val($("input[name='nameEng']:checked").val());
			var dataItem = $("input[name='nameEng']:checked").val();
			var dataItemCls = "." + dataItem + "_itmename";
			var dataJanIdItemCls = "." + dataItem + "_radio_input_jan_id";
			var hiddenInputJanid = $(dataJanIdItemCls).attr("data-jan_id");
			$("#jan_id").val(hiddenInputJanid); //jan_id
			var name = $(dataItemCls).text();
			$("#janaadharfeatchedname").html("<span style='color: #0b10f1;font-size: 15px;border: 2px solid #0f22d7;padding: 4px;text-align: center;float: left;width: 100%;font-weight: bold;'>  Candidate Name : " + name);
			$('#modal1').find('.modalRegistraion').html("");
			$('#modal1').find('.modalFooter').html("");
			$('#modal1').modal('close');
		} 
	});
	
	$("#btnSearchJanAadharBtn").focusout(function(){
		var jan_aadhar_nubmer = $("#jan_aadhar_number").val();
		if(jan_aadhar_nubmer.length == 0 ){
			swal({
				title: "Jan Aadhar Number Required",
				text: "Jan Aadhar Number should be enter first.",
				icon: "error", 
				allowOutsideClick: false,
				button: "Clear & Re-Enter",
				timer: 30000
			}); 
			return false;
		}
		if(jan_aadhar_nubmer.length > 0 && jan_aadhar_nubmer.length < 10){
			swal({
				title: "Jan Aadhar Number Invalid",
				text: "Jan Aadhar Number should be 10 digits.",
				icon: "error", 
				allowOutsideClick: false,
				button: "Clear & Re-Enter",
				timer: 30000
			}); 
			return false;
		}
	});

	$("#jan_aadhar_number").focusout(function(){
		showLoading();
		var jan_aadhar_nubmer = $("#jan_aadhar_number").val();
		var length = jan_aadhar_nubmer.length;
		if(jan_aadhar_nubmer.length == 10){ 
			var action = "getJanAadharDetails";
			var ajaxUrl = ajaxBaseUrl + action  + "/"+ jan_aadhar_nubmer;
			$.ajax({
				url: ajaxUrl,
				type: "GET",
				success: function (response) { 
					hideLoading();
					var htmlFooter = "";
					if(response.cmsg != 110){
						var entDetails = response.entitlementInfo.entDetail;
						var members = response.personalInfo.member;
						var html = "<table class='table'>";
						html += "<tr><th colspan='8'><b><center>Please select any one member and press Continue button</center></b></th><tr>";
						html += "<tr><th width='50px'>Choose</th><th width='50px'>#</th><th>Name</th><th>Father's Name</th><th>Mother's Name</th><th>DOB</th><th>Aadhar</th><tr>";
						
						var counter = 0;
						var keyCounter = 0;
						var response_type = Object.prototype.toString.call(members);
						if(response_type=='[object Object]'){ // single member loop
							html += "<tr>";
							counter++;
							$.each(members, function(k, v) { 
								var cls = 'inputOption' + counter;
								if(k == "nameEng"){	
									html += '<td><p class="mb-1 inputOption' + counter +'"><label><input data-jan_id="' + entDetails[keyCounter].jan_mid +'" data-id="' + counter +'" class="memberchooseradioinput with-gap memberRadioOption ' + counter + '_radio_input_jan_id" value="' + counter + '" name="' + k + '" type="radio"><span></span></label></p></td>';
									html += '<td>' + (counter) + '</td>';									
									html += "<td class='" + counter + "_itmename'><b>" + v +"</b></td>";
								}
								if(k == "fnameEng"){
									html += "<td>" + v +"</td>";
								}
								if(k == "mnameEng"){
									html += "<td>" + v +"</td>";
								}
								if(k == "aadhar"){
									html += "<td>" + v +"</td>"; 
									if(v != ""){
										var action = "_isAadharNumberExists";
										var ajaxUrl = ajaxBaseUrl + action  + "/"+ v;
										$.ajax({
											url: ajaxUrl,
											type: "GET",
											success: function (response2) {
												if(response2 != 0){
													$("."+cls).html("<span style='color:green;'>Already</span>");
												}
											},
											error: function (data) {
												toastr.error(data.success);
												console.log('Error:', data); 
												hideLoading();
											}
										});
									}

								}
								if(k == "dob"){
									html += "<td>" + v +"</td>";
								}
							});
							html += "</tr>";
						} else {   // multiple member loop 
							$.each(members, function() {   
								html += "<tr>";
								counter++;
								$.each(this, function(k, v) { //entDetails[keyCounter].jan_mid
									var cls = 'inputOption' + counter;
									if(k == "nameEng"){	
										html += '<td><p class="mb-1 inputOption' + counter +'"><label><input data-jan_id="' + entDetails[keyCounter].jan_mid +'" data-id="' + counter +'" class="memberchooseradioinput with-gap memberRadioOption ' + counter + '_radio_input_jan_id" value="' + counter + '" name="' + k + '" type="radio"><span></span></label></p></td>';
										html += '<td>' + (counter) + '</td>';									
										html += "<td class='" + counter + "_itmename'><b>" + v +"</b></td>";
									}
									if(k == "fnameEng"){
										html += "<td>" + v +"</td>";
									}
									if(k == "mnameEng"){
										html += "<td>" + v +"</td>";
									}
									if(k == "aadhar"){
										html += "<td>" + v +"</td>"; 
										if(v != ""){
											var action = "_isAadharNumberExists";
											var ajaxUrl = ajaxBaseUrl + action  + "/"+ v;
											$.ajax({
												url: ajaxUrl,
												type: "GET",
												success: function (response2) {
													if(response2 != 0){
														$("."+cls).html("<span style='color:green;'>Already</span>");
													}
												},
												error: function (data) {
													toastr.error(data.success);
													console.log('Error:', data); 
													hideLoading();
												}
											});
										}

									}
									if(k == "dob"){
										html += "<td>" + v +"</td>";
									}
								});
								html += "</tr>";
								keyCounter++;
							});
						}
						html += "</table>"; 
						htmlFooter += '<div class="col m10 s12 mb-3">';
						htmlFooter += '<button class="btn cyan waves-effect waves-light right " type="button" id="janAadharAction">Continue';
						htmlFooter += '</button></div>';
						$('.submitBtn').removeClass('disabled');
						$('#modal1').find('.modalRegistraion').html(html);
						$('#modal1').find('.modalFooter').html(htmlFooter);
						$('#modal1').modal('open');
						return false;
					}else{
						var message = "Entered Jan Aadhar Number ("+jan_aadhar_nubmer + ")  is invalid.";
						$("#jan_aadhar_number").val("");
						$( "#jan_aadhar_number" ).focus();
						swal({
							title: "Jan Aadhar Number Invalid",
							text: message,
							icon: "error", 
							allowOutsideClick: false,
							button: "Clear & Re-Enter",
							timer: 30000
						}); 
						return false;
					}
				},
				error: function (data) {
					toastr.error(data.success);
					console.log('Error:', data); 
					hideLoading();
				}
			}); 
		}else{ 
			var jan_aadhar_nubmer = $("#jan_aadhar_number").val();
			if(jan_aadhar_nubmer.length > 0 && jan_aadhar_nubmer.length < 10){
				swal({
					title: "Jan Aadhar Number Invalid",
					text: "Jan Aadhar Number should be 10 digits.",
					icon: "error", 
					allowOutsideClick: false,
					button: "Clear & Re-Enter",
					timer: 30000
				}); 
				return false;
			}
		}
	});

	$(document).ready(function() {
		formId = "#" + config.data.formId; 
		$(formId).validate({
			rules: {
				jan_aadhar_number:"required",
				adm_type:"required",
				course: "required",
				stream: "required",
				are_you_from_rajasthan: "required",
			},
			errorElement: 'div', 
			messages: {
				// jan_aadhar_number: { 
				// 	required: "Jan Aadhar Nubmer is required", 
				// 	minlength: "Jan Aadhar Nubmer must be of 10 character",
				// 	maxlength: "Jan Aadhar Nubmer cannot be more than 10 character",
				// },
				jan_aadhar_number: "Jan Aadhar Nubmer is required",
				adm_type: "Please select the Admission Type",
				course: "Please select the Course Type",
				stream: "Please select the Stream Type",
				are_you_from_rajasthan: "Please select the Are you from Rajasthan",
				
			},
			success: function(response){
				
			},
			submitHandler: function(form) {
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
					}).then((willsave) => { 
						if (willsave) { 
							$(formId).addClass(clsName);
							$(formId).submit();							
							
						}
					});
				}
			}
			//submitHandler: function(form) {
				//var formId = "#" + config.data.formId; 
				//var action = "checkRegistration";
				//var ajaxUrl = ajaxBaseUrl + action;
				//$.ajax({
					//url: ajaxUrl,
					//type: "POST",
					//data: $(formId).serialize(),
					//success: function (response) { 
						//hideLoading();
						//if(response[0]['status'] == false){
							//var errors = response[0]['error'];
							// alert(response[0]['error'].ssoid);
							//$("#validation-errors-div").removeClass("hide");
							//$('#validation-errors').append('<i class="material-icons"></i> ');
							//$.each(errors, function(key,value) { 
								 //$('#validation-errors').append('<div class="alert alert-danger">'+value+'</div');
							//}); 
						//}else {
							//$(formId).submit();
						//} 
					//},
					//error: function (data) {
						//toastr.error(data.success);
						//console.log('Error:', data); 
						//hideLoading();
					//}
				//});
				
			//}
		});
	});
	
// </script>



