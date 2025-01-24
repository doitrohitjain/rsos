$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "finalresultupdatevalidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + "finalupdate";
   
	
	
	$(formId).validate({
        rules: {
            total_marks:{
                required: true,
            },
            final_result:{
                required: true,
            },
            percent_marks:{
                required: true,
            },
            

        }, 
		messages: {
            final_result:{
                required:"final results is required ",
            },
            total_marks:{
                required: "Total Marks is Required",
            },
            percent_marks:{
                required: "Percentage  is Required",
            },
            
        },
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.finalresultupdatevalidation,
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




