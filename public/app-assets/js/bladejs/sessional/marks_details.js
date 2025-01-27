$(document).ready(function() {
    var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
    formId = "#" + config.data.formId;
     
    var action = "ajaxSessinalMarksValidation";
    var ajaxUrl = ajaxBaseUrl + action;

    $(formId).validate({
        success: function(response){},
        submitHandler: function(form) { 
            if($('#ajaxRequest').val()==1){
                showLoading(); 
                $.ajax({
                    url: config.routes.ajaxSessinalMarksValidation,
                    type: "POST",
                    dataType: "json",
                    data: $(formId).serialize(),
                    success: function (response) {
                        hideLoading(); 
                        if(response.isValid==false){
                            var message = response.error.subject_id;
                            swal({
                                title: "Validation Error",
                                text: message,
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
                        // console.log('Error:', data); 
                        hideLoading();
                    }
                });
            } else {
                return true;
            } 
        }
    });
});

    
 


$('.allowSpeChar').keyup(function() {
    var raw_text =  $(this).val();
    var return_text = raw_text.replace(/[^a-bA-B0-9]/g,'');
    $(this).val(return_text);
}); 


$('.toc_total_calculation').on("keyup", function() {  
    var id = $(this).attr('id').replace(/[^0-9.]/g, "");
    
    var theory_marks = 0;
    if($('#theory_'+id).val()!=''){
        var theory_marks = parseInt($('#theory_'+id).val());
    }
    
    var practical_marks = 0;
    if($('#practical_'+id).val()!=''){
        var practical_marks = parseInt($('#practical_'+id).val());
    }
    
    if(parseInt(theory_marks+practical_marks) > 100){
        $(this).val('');
        alert('Total marks should be less than or equal 100.');
        $('#total_'+id).val('');
        return false;
    }
    
    $('#total_'+id).val(parseInt(theory_marks+practical_marks));
}); 


$('.sessional_marks').on("change", function(event) {
    var row_id = '';
    if($(this).val()=='' || $(this).val()=='undefined') { 
        $(this).val('');
        return false;
    }
    var input_marks  = ($(this).val());
    var input_marks_temp  = ($(this).val());
    var max  = parseInt($(this).attr('data-max'));
    var min  = parseInt($(this).attr('data-min'));
   
    if(/ab/.test(input_marks_temp) || /ba/.test(input_marks_temp) || /BA/.test(input_marks_temp)  || /a/.test(input_marks_temp) || /b/.test(input_marks_temp) || /A/.test(input_marks_temp) || /B/.test(input_marks_temp) ){
        if(input_marks != 'AB' || input_marks == 'BA'){
            message = "Please enter 'AB' insted of " + input_marks;
            swal({
                title: "Validation Error",
                text: message,
                icon: "error",
                button: "Close",
                timer: 30000
            });
            $(this).val('');
            return false;
        }
    }
    

    var message = "Marks should be between maximum " + max + " & minimum " + min + ".";
    // if(max > input_marks || min < input_marks){
	if(max < input_marks || min > input_marks){
		$(this).val('');
        swal({
            title: "Validation Error",
            text: message,
            icon: "error",
            button: "Close",
            timer: 30000
        });
        $(this).focus();
        return false; 
    }      
});

    
     