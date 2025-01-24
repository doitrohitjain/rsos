// jQuery Form Validator
$(document).ready(function() {
    formId = "#" + "marksheet_correction";
    $(formId).validate({ 
        submitHandler: function (form) {
            var clsName = "api"; 
            if($(formId).hasClass(clsName)){
                return true;
            }else{
                event.preventDefault();
                swal({
                    title: 'क्या आप निश्चित हैं कि ' + application_fee +'/- का भुगतान जारी रखना चाहते हैं?(Are you certain you wish to proceed with the payment ' + application_fee +'/-)?',
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
