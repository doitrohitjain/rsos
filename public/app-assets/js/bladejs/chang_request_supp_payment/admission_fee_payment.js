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
			if($("#aadhar_number").val() == "" && $("#jan_aadhar_number").val() == ""){
				message = (" कृपया * अनिवार्य फ़ील्ड भरें आधार नंबर या जन आधार नंबर। (Please fill * mandatory filled. Aadhar Number Or Jan Aadhar Nubmber.)");
                swal({
                    title: "Validation Error",
                    text: message,
                    icon: "error",
                    button: "Close",
                    timer: 30000
                });
                return false;
			}
		}

		if(($("#aadhar_number").val().length >= 12 && $("#aadhar_number").val().length <= 16) || ($("#jan_aadhar_number").val().length >= 10 && $("#jan_aadhar_number").val().length == 10)){
			
		}else{
			// message = (" कृपया आधार संख्या या जन आधार संख्या के मान्य अंक भरें।( Please fill in the valid digits of aadhar number or jan aadhar nubmer.)");
            // swal({
            //     title: "Validation Error",
            //     text: message,
            //     icon: "error",
            //     button: "Close",
            //     timer: 30000
            // });
            // return false;
		}
	}); 



});