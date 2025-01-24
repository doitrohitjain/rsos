$(document).ready(function() {

/* selection ai center start */
  $('.selectaicenter').on('click',function(){
	var ai_code = $(this).attr('data-ai_code');
	var aidetails = $(this).attr('data-aidetails');
	var formId = "#self_re"+ai_code; 
	
    showLoading();
	var clsName = "api";
	swal({
		title: 'Would you like to continue with the selected AI Centre?(क्या आप चयनित एआई केंद्र को जारी रखना चाहेंगे?)',
		text: aidetails,
		icon: 'info',
		buttons: ["Cancel", "Continue"],
	})
	  .then((willsave) => {
		if (willsave) {  
			$(formId).addClass(clsName); 
			$('#ajaxRequest').val('0');
			$(formId).submit();
		}
	  });  

	hideLoading(); 
  });
/* selection ai center end */ 


  $('#temp_district_id').on('change',function(){
    //$("#temp_block_id").html('<option value="">Select Block(खंड)</option>');
    var district_id = this.value;
	var req_type = "is_allow_for_admission";
    if(true || district_id != ''){
		$("#temp_block_id").html('<option value="">Select Block(खंड)</option>');
		$.ajax({
            url: config.routes.getTempblock,
            type: "get",
            data: {'id': district_id,'req_type': req_type},
            dataType : 'json',
            success: function (result){
              $("#temp_block_id").html('<option value="">Select Block(खंड)</option>');
              $.each(result,function(key,value){
                $('#temp_block_id').append('<option value="' + key + '">' +value+'</option>');
                $("#temp_block_id").trigger('contentChanged');
              });	
            },
          });
		$.ajax({
            url: config.routes.getTempdistrictaicenter,
            type: "get",
			data: {'id': district_id,'req_type': req_type},
            dataType : 'json',
            success: function (result){
              $(".ai_code").html('<option value="">Select AI Centre(एआई सेंटर)</option>');
              $.each(result,function(key,value){
                $('.ai_code').append('<option value="' + key + '">' +value+'</option>');
                $(".ai_code").trigger('contentChanged');
              });	
            },
          });
    }
  });
});




$('#temp_district_id, #temp_block_id').on('change', function(){
	var district_id = $("#temp_district_id").val();
	var block_id = $("#temp_block_id").val();
	var req_type = "is_allow_for_admission";
	// $('.get_Aicnetervalue').html(""); 
	if(true || district_id != '' && block_id != ''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.gettempaicode,
			data: { district_id: district_id,block_id: block_id,req_type: req_type},
			success: function (result){
                $(".ai_code").html('<option value="">Select AI Centre(एआई सेंटर)</option>');
                $.each(result,function(key,value){
                  $('.ai_code').append('<option value="' + key + '">' +value+'</option>');
                  $(".ai_code").trigger('contentChanged');
                });	
              },
		});
	}else{
		$(".ai_code").html('<option value="">Select AI Centre(एआई सेंटर)</option>');
		$(".ai_code").trigger('contentChanged');
	}
});


$('.aicenter').on('change', function(){
	$('.get_Aicnetervalue').html("");
	var aicode = $(".aicenter  option:selected").text();
	if(aicode == "Select Ai Center"){
		$('.get_Aicnetervalue').html("");
		$("#aiCenterMsg").addClass('hide');
	}else{
		$('.get_Aicnetervalue').html(aicode); 
		$("#aiCenterMsg").removeClass('hide');
	}
});







