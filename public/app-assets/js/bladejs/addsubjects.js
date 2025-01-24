$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "AddsubjectValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
    formId = "#" + "addsubjects";
	$(formId).validate({
        rules: {
            subject_id: {
            required: true,
            },   
            exam_year: {
                required: true,
            },   
            exam_month: {
                required: true,
            },             

        }, 
        messages: {
            subject_id: {
                required: "subject is required",
            },
            exam_year: {
                required: "Exam Year is required",
            },
            subject_id: {
                required: "exam_month is required",
            },
            
            
        },
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.AddsubjectValidation,
					type: "post",
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
								title: 'Are you sure you want to save your information.',
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

$('.checksubject').on("change", function(event) { 
    var student_id=$("#studentid").val();
    var subject_id=$("#subjectid").val();
    showLoading(); 
			$.ajax({
				url: config.routes.getstudentsubjectdata,
				type: "get",
				data: {'subject_id': subject_id,'student_id': student_id},
				dataType : 'json',
				success: function (result){
                    if(result == 1){

                    }else{
                        $('#subjectid').val("").trigger('change');
                        swal({
                            title: "Validation Error",
                            text: 'This Subject is allready exists is exam subjects.',
                            icon: "error",
                            button: "Close",
                            timer: 30000
                        }).then((willsave) => {
                            location.reload();
                        });
                        return false;




                    }
				//    if(typeof(result.ssoid)  === "undefined"){}else{ 
				// 	   $('.examiner_name').val(result.name);
				// 	   $('.mobile').val(result.mobile);
				// 	   $('.mapping_examiner_id').val(result.id);
				// 	   $('.ischanged').prop("checked", true); 
				// 	   $('.sso_id').val(result.ssoid).trigger('change');
				// 	   $(".isChangedCls").removeClass("hide");
				//    }
			   },error: function (response) {
					console.log('Error:', response); 
					hideLoading();
				   	return false;
				}
			});	


  
// });
});