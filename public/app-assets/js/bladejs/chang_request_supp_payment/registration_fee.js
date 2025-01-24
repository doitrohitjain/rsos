// jQuery Form Validator
$(document).ready(function() {
    formId = "#" + config.data.formId;
    $(formId).validate({ 
        submitHandler: function (form) {
            var clsName = "api"; 
            if($(formId).hasClass(clsName)){
                return true;
            }else{
                event.preventDefault();
                swal({
                    title: 'क्या आप ' + application_fee +'/- का भुगतान करना चाहते हैं?(Are you sure you want to pay ' + application_fee +'/-)?',
                    text: "आप इसे वापस नहीं कर पाएंगे!(You won't be able to revert this!)",
                    icon: 'success',
                    buttons: true,
                })
                .then((willsave) => {
                    if (willsave) {
                    
                        $(formId).addClass(clsName); 
                        $(formId).submit();
                    }
                });
            }
        }
    });
});
