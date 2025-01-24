
$(document).ready(function() {
    formId = "#" + config.data.formId; 
    $(formId).validate({
        rules: {
            enrollment: { required: true,digits:true, minlength:10,maxlength:50}
        }, 
        messages: {
            enrollment: {
                required: "Please enter enrollment number",
                minlength: "The enrollment number at least 11 digits",
                digits: "Please enter only numbers",
                maxlength: "The enrollment number maximum 11 digits",
            } 
        },
        success: function(response){
        },
        submitHandler: function(form) {
                //return true;
                if($('#ajaxRequest').val()==1){
                    showLoading(); 
                    $.ajax({
                        url: config.routes.checksessionaltudent,
                        type: "POST",
                        data: $(formId).serialize(),
                        success: function (data) {
                            if(data == false){
                              swal({
                        title: 'Enrollment Number Not Found.',
                        icon: 'error',
                        button: "Close",
                        });
                    } else {
                            var clsName = "api";

                            if($(formId).hasClass(clsName)){

                            return true;
                            }
                            swal({
                                title: 'Are you sure save your information.',
                                text: "",
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
    
