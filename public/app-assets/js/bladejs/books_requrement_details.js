

$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxBooksRequrementDetilasValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;

	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxBooksRequrementDetilasValidation,
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
								title: 'Are you sure save your information.',
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

$(document).ready(function() {
    $('.course_id').on('change',function(){
        var courseid = this.value;
        $(".subject_id").html('<option value="">Select Subject</option>');
    $.ajax({
              url: config.routes.getsubjects,
              type: "get",
              data: {'id': courseid},
              dataType : 'json',
              success: function (result){
                $(".subject_id").html('<option value="">Select Subject</option>');
                $.each(result,function(key,value){
                  $('.subject_id').append('<option value="' + key + '">' +value+'</option>');
                  $(".subject_id").trigger('contentChanged');
                });	
              },
            });
       
       });
});

$('.allsubjectcoursemedium').on('change', function(){
	var course = $(".courses").val();
	var subject = $(".subjects").val();
	var aicode = $(".aicode").val();
	var medium = 'Hindi';	
	var hindi = 'हिंदी';
	$(".volume").html('<option value="">Select Volume</option>');
	if(course!='' && subject!='' && aicode!=''){
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.expectedstudentcountdata,
			data: { course: course,subject: subject,aicode:aicode},
			success: function(data) {
				hideLoading();
				$(".volume").html('<option value="">Select Volume</option>');
                $.each(data.volumes,function(key,value){
                  $('.volume').append('<option value="' + key + '">' +value+'</option>');
                  $(".volume").trigger('contentChanged');
                });	
				$('#medium').val(data.medium); 
				if(data.medium == 1){
					medium = '';
					hindi  = '';
					var datavalue = data.hindi + data.engilsh;
					$(".hindiautostudentcount").val(datavalue);
					$("#englishBox").hide();
				}else{		
					$(".hindiautostudentcount").val(data.hindi);
					$(".englishautostudentcount").val(data.engilsh);
					$("#englishBox").show();
				}
				$('#enrollmentstudentcount').text(medium + ' ' + "Enrollment Student Count("+ hindi + ' ' +"नामांकन छात्र संख्या) ");

				$('#lastyearbookcount').text(medium +' ' + "Last Year Book Stock Count(" + hindi + ' ' + "पिछले वर्ष की पुस्तक स्टॉक संख्या)");

				$('#requiredbookcount').text(medium + ' ' +"Required Book Count (" + hindi + ' ' +"वर्तमान सत्र में शुद्ध मांग)");
				$('#hindilastyearstockcount').attr('placeholder',medium + ' ' + 'Last Year Book Stock Count('+ hindi + ' ' +'पिछले वर्ष की पुस्तक स्टॉक संख्या)');

				$('.hindirequiredbookcount').attr('placeholder', medium + ' ' + "Required Book Count ("+ hindi + ' ' + "वर्तमान सत्र में शुद्ध मांग)");

				$('.hindiautostudentcount').attr('placeholder',medium + ' ' + "Enrollment Student Count(" + hindi + ' ' +"नामांकन छात्र संख्या) ");
			}
		});
	}
});


$(document).ready(function() {
	$('.checkcombinationexists').on('change',function() {

		if( $(".course_id").val() !='' && $(".subject_id").val()!='' && $(".volume").val()!='' && $(".midiums").val()!=''){
			var course_id=$(".course_id").val();
			var subject_id=$(".subject_id").val();
			var volume_id=$(".volume").val()
			var ai_code=$(".aicode").val();
			var publicbookid=$(".publicbookid").val();
			
		 showLoading(); 
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});	
			$.ajax({
				url: config.routes.checkPublishBookdata,
				type: 'get',
				data: {'course':course_id,'subject_id':subject_id,'ai_code':ai_code,'volume_id':volume_id,'publicbookid':publicbookid},
				dataType : 'json',
				success: function(result){ 
					if(result==true){
						swal({
							title: "Validation Error",
							text: "Selected Combination already Exists.",
							icon: "error",
							button: "Close",
							timer: 30000
						}).then((result) => {
							if(result == true){
								 // this is your location reload.
							}
						}) 
					}else{

						return false;
					}
				
				}

			});
		}
	});

});

function hindirequiredbookcountcalculate(){
	var lastyearstockcount=$("#hindilastyearstockcount").val();
	var hindiautocount=$(".hindiautostudentcount").val();
	hindiautocount = parseInt(hindiautocount);
	lastyearstockcount=parseInt(lastyearstockcount);
	
	var totalhindicount =null;
	if(hindiautocount  > lastyearstockcount){
	     totalhindicount =  hindiautocount - lastyearstockcount;
	}else{
		
		totalhindicount=0;
	}
	$(".hindirequiredbookcount").val(totalhindicount);
}


function englishrequiredbookcountcalculate(){
	var englishautocount = $(".englishautostudentcount").val();
	var englishlaststockcount=$("#englishlastyearstockcount").val();
	englishautocount=parseInt(englishautocount);
	englishlaststockcount=parseInt(englishlaststockcount);
	var englishcount =null;
	if(englishautocount  > englishlaststockcount){
	    englishcount =  englishautocount - englishlaststockcount;
	}else{
		englishcount=0;
	}
	$(".enlgishrequiredbookcount").val(englishcount);
}

$(document).ready(function() {
	$('#volumes').on('change',function() {
    var vartext = document.getElementById("hindilastyearstockcount");
	});

});










