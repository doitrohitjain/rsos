$(document).ready(function() { 
    $("#Student").on('submit',function(){ 
		var error = 0;
		$( ".requried" ).each(function() {
			if($(this).val() == ""){
				error = 1;
			}
		});  
		if(error == 1 ){
			message = ("कृपया * अनिवार्य फ़ील्ड भरें(Please fill * mandatory fields.)");
            swal({
                title: "Validation Error",
                text: message,
                icon: "error",
                button: "Close",
                timer: 30000
            });
            return false;
		}else{
			 
		} 
	}); 



});

$(function() {
	$( ".datepicker" ).datepicker({  maxDate: new Date() });
   });