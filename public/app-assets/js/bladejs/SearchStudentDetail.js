  // jQuery Form Validator
  $(document).ready(function() {
    var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
    var formId = "#searchstudentdetail";
    var action = "searchstudentdata";
   
    var ajaxUrl = ajaxBaseUrl + action;
            
    $(formId).validate({
        rules: {
            enrollment: { required: true,digits:true,minlength: 11 ,maxlength:15 },
        }, 
        messages: {
            enrollment: { 
                required: "Please enter enrollment number", 
            },
            captcha: "Please Enter Captcha.",
            
        },
        success: function(response){
            
        },
        submitHandler: function(form) { 
            //return true;
            if($('#ajaxRequest').val()==1){
                showLoading(); 
                $.ajax({
                    url: config.routes.searchstudentdata,
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

// </script>
