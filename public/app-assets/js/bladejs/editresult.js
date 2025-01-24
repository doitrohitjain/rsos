$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "updateStudentSubjectsDataValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
    formId = "#" + "editresult";
	$(formId).validate({
        rules: {
            sessional_marks: {
            required: true,
                maxlength: 20,
            },
            sessional_marks_reil_result:{
                required: true,
            },
            final_practical_marks:{
                required: true,
            },
            final_theory_marks:{
                required: true,
            },
            final_result:{
                required: true,
            }
            

        }, 
        messages: {
            sessional_marks: {
                required: "Sessional Marks is Required",
            },
			sessional_marks_reil_result:{
				required:"Sessional Marks Reil Result is Required",
			},
 			final_practical_marks:{
 				required: "Final Practical Marks is Required",
		    },
			final_theory_marks:{
				required: "Final Theory Marks is Required",
		    },
            total_marks:{
                required: "Total Marks is Required",
            },
            final_result:{
                required: "Final Result is Required",
            },
            
        },
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.updateStudentSubjectsDataValidation,
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



$(function(){
	$('.toggle-class').change(function() {
		var status = $(this).prop('checked') == true ? 1 : 0; 
		var user_id = $(this).data('id'); 
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: "POST",
			url: SITEURL+'ajax/change_status',
			data: {'status': status, 'user_id': user_id},
			success: function(data){
				console.log(data.success)
		   }
		});           
	}) 
})

function totalmarks(){
	
	var sessionalMarks=$("#ExamSubjectSessionalMarksReilResult").val();
	var practicalMarks=$("#ExamSubjectFinalPracticalMarks").val();
	var theoryMarks= $("#ExamSubjectFinalTheoryMarks").val();
	sessionalMarks =(sessionalMarks >0 && sessionalMarks <= 10)?sessionalMarks:0;
	practicalMarks =(practicalMarks >0 && practicalMarks <= 100)?practicalMarks:0;
	theoryMarks =(theoryMarks >0 && theoryMarks <= 100)?theoryMarks:0;
	var totalmarks=eval(sessionalMarks)+eval(practicalMarks)+eval(theoryMarks);
	$("#ExamSubjectTotalMarks").val(totalmarks);
}

$('.theorymark').on("change", function(event) { 
        var row_id = '';
        if($(this).val()=='' || $(this).val()=='undefined') { 
            $(this).val('');
            // $(this).focus();
            return false;
        }
        input_marks= parseInt($(this).val());
        stream=parseInt(stream);
        toc=tocdata;
        theory_max_marks1 = parseInt(theory_max_marks) - parseInt(sessional_max_marks);
        theory_max_marks2=parseInt(theory_max_marks);
        if(input_marks !=777 || input_marks !=888){
          if(input_marks !=999){           
            if(input_marks > theory_max_marks1 && stream==1 && toc== ""){
                swal({
                    title: "Validation Error",
                    text: 'Please Enter Marks should be less than   ' + theory_max_marks1 + ' marks.',
                    icon: "error",
                    button: "Close",
                    timer: 30000
                })
                $("#ExamSubjectFinalTheoryMarks").val('');
                $(this).focus();
                return false;
            }
            if(input_marks > theory_max_marks2 && stream==1 && toc != " "){
                swal({
                    title: "Validation Error",
                    text: 'Please Enter Marks should be less than   ' + theory_max_marks2 + ' marks.',
                    icon: "error",
                    button: "Close",
                    timer: 30000
                })
                $("#ExamSubjectFinalTheoryMarks").val('');
                $(this).focus();
                return false;
            }
            if(input_marks > theory_max_marks2 && stream==2){
                swal({
                    title: "Validation Error",
                    text: 'Please Enter Marks should be less than   ' + theory_max_marks2 + ' marks.',
                    icon: "error",
                    button: "Close",
                    timer: 30000
                })
                $("#ExamSubjectFinalTheoryMarks").val('');
                $(this).focus();
                return false;
            }
        }
        }

        
    // });
});



$('.practicalmax').on("change", function(event) { 
    var row_id = '';
    if($(this).val()=='' || $(this).val()=='undefined') { 
        $(this).val('');
        // $(this).focus();
        return false;
    }
    practical_max_marks = parseInt(practical_max_marks);
    input_marks  = parseInt($(this).val()); 
    prctical_type=parseInt(prctical_type);
    if(prctical_type == 0){
        if(input_marks!=999){
            swal({
                title: "Validation Error",
                text: 'This subjects is not a Prctical subject',
                icon: "error",
                button: "Close",
                timer: 30000
            })
            $(this).val('');
            $(this).focus();
            return false;
        }
    }
    

    if(input_marks <= practical_max_marks || input_marks==999){
        
    }else { 
        swal({
            title: "Validation Error",
            text: 'Please Enter Marks should be less than   ' + practical_max_marks + ' marks.',
            icon: "error",
            button: "Close",
            timer: 30000
        })
        $(this).val('');
        $(this).focus();
        return false;
    }

  
// });
});



$('.sessonialmarks').on("change", function(event) { 
    var row_id = '';
    var sessionalMarks1=parseInt($("#sessionalmarks1").val());
	var sessionalMarksreiilresults=parseInt($("#ExamSubjectSessionalMarksReilResult").val());
    if($(this).val()=='' || $(this).val()=='undefined') { 
        $(this).val('');
        // $(this).focus();
        return false;
    }
    sessional_max_marks = parseInt(sessional_max_marks);
    input_marks  = parseInt($(this).val()); 
    if(input_marks <= sessional_max_marks){
        
    }else { 
        swal({
            title: "Validation Error",
            text: 'Please Enter Sessional Marks should be less than   ' +  sessional_max_marks + ' marks.',
            icon: "error",
            button: "Close",
            timer: 30000
        })
        $(this).val('');
        $(this).focus();
        return false;
    }
    // if(sessionalMarks1 == sessionalMarksreiilresults){  
    // }else{ 
    //     swal({
    //         title: "Validation Error",
    //         text: 'Sessional Marks is equal to Sessional Marks Reil Result',
    //         icon: "error",
    //         button: "Close",
    //         timer: 30000
    //     })
    //     $(this).val('');
    //     $(this).focus();
    //     return false;
    // }
});





