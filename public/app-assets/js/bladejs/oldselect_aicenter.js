$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "AjaxSelfRegistrationValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "self_re"; 

	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 

			var district_id =  $('.district_id').val();
			var block_id =  $('.block_id').val();
			var aicode =  $('.aicode').val();
			if(aicode == "" || district_id == "" || block_id == ""){
				swal({
					title: "Validation Error",
					text: "कृपया तारक (*) से चिह्नित सभी अनिवार्य फ़ील्ड भरें।(Please fill in all mandatory fields marked with an asterisk (*).)",
					icon: "error",
					button: "Close",
					timer: 30000
				});
				return false;
			}
			
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.AjaxSelfRegistrationValidation,
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
								title: 'Would you like to continue?',
								text: "Would you like to continue with your selected AI center?(क्या आप अपने चयनित एआई केंद्र को जारी रखना चाहेंगे?)",
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
						//console.log('Error:', data); 
						hideLoading();
					}
				});
			} else {
				return true;
			} 
			
		}
	});
});

$(document).ready(function() {
    $('.district_id').on('change',function(){
        var district_id = this.value;
        $(".block_id").html('<option value="">Select Block</option>');
       $.ajax({
              url: config.routes.getblock,
              type: "get",
              data: {'id': district_id},
              dataType : 'json',
              success: function (result){
                $(".block_id").html('<option value="">Select Block</option>');
                $.each(result,function(key,value){
                  $('.block_id').append('<option value="' + key + '">' +value+'</option>');
                  $(".block_id").trigger('contentChanged');
                });	
              },
            });
       
       });
});

$('.location').on('change', function(){
	var district_id = $(".district_id").val();
	var block_id = $(".block_id").val();
	$('.get_Aicnetervalue').html(""); 
	if(district_id != '' && block_id != ''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.getaicode,
			data: { district_id: district_id,block_id: block_id},
			success: function (result){
                $(".aicode").html('<option value="">Select Ai Center</option>');
                $.each(result,function(key,value){
                  $('.aicode').append('<option value="' + key + '">' +value+'</option>');
                  $(".aicode").trigger('contentChanged');
                });	
              },
		});
	}else{
		$(".aicode").html('<option value="">Select Ai Center</option>');
		$(".aicode").trigger('contentChanged');
	}
});


$('.aicenter').on('change', function(){
	$('.get_Aicnetervalue').html("");
	var aicode = $(".aicenter  option:selected").text();
	aicode = aicode.replace("Contact:", '<br><span style="color:brown;">');
	
	if(aicode == "Select Ai Center"){
		$('.get_Aicnetervalue').html("");
		$("#aiCenterMsg").addClass('hide');
	}else{
		$('.get_Aicnetervalue').html(aicode); 
		$("#aiCenterMsg").removeClass('hide');
	}
});







