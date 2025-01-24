// $(document).ready(function() {
// 	$("#form_id").validate({
// 		rules: {
// 			enrollment: {
// 				required: true,
// 				// maxlength: 20,
// 			},
// 			course:{
// 				required: true,
// 			},
// 			subjects:{
// 				required: true,
// 			},
// 			sessional_marks: {
// 				required: true,
// 				// maxlength: 20,
// 				},
// 			sessional_marks_reil_result:{
// 				required: true,
// 			},
// 			final_practical_marks:{
// 				required: true,
// 			},
// 			final_theory_marks:{
// 				required: true,
// 			},
// 			total_marks:{
// 				required: true,
// 			},
// 			final_result:{
// 				required: true,
// 			},
// 			percent_marks:{
// 				required: true,
// 			},
// 		},
// 		messages: {
// 			final_result:{
// 				required:"Please Select  final results",
// 			},
// 			enrollment:{
// 				required:"Enrollment is required",
// 			},
// 			course:{
// 				required:"Course is required",
// 			},
// 			subjects:{
// 				required:"Subjects is required",
// 			},
// 			sessional_marks: {
// 				required: "Sessional Marks is Required",
				
// 				},
// 			sessional_marks_reil_result:{
// 				required:"Sessional Marks Reil Result is Required",
// 			},
// 			final_practical_marks:{
// 				required: "Final Practical Marks is Required",
// 			},
// 			final_theory_marks:{
// 				required: "Final Theory Marks is Required",
// 			},
// 			total_marks:{
// 				required: "Total Marks is Required",
// 			},
			
			
// 		}
// 	});
// });
$(document).ready(function() {
    var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var formId = "#form_id";
	var action = "getstudentexamsubjectdata";
    var ajaxUrl = ajaxBaseUrl + action;
            
    $(formId).validate({
        rules: {
            enrollment: { required: true,digits:true,minlength: 11 ,maxlength:15 },
        }, 
        messages: {
            enrollment: { 
                required: "Please enter enrollment number", 
            },
            
        },
        success: function(response){
            
        },
        submitHandler: function(form) { 
            //return true;
            if($('#ajaxRequest').val()==1){
                showLoading(); 
                $.ajax({
                    url: config.routes.getstudentexamsubjectdata,
                    type: "POST",
                    data: $(formId).serialize(),
                    success: function (data) {
                    if(data == false){
                        swal({
                            title: 'Data not found as per given details.',
                            icon: 'error',
                            button: "Close",
                        });
                    } else {
                        var clsName = "api";

                        if($(formId).hasClass(clsName)){

                        return true;
                        }
                        swal({
                            title: 'Are you sure you want to update data.',
                            text: "Please confirm",
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


