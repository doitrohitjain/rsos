$(document).ready(function() { 
    $('#button1').click(function() {
	 var getdatvalue = $('#getvalue').val(); 
	  if(getdatvalue == ""){
			alert("Please enter query");
			return false;
		}
		$("#getexcel").val($("#getexcel").val()+getdatvalue);
		
    }); 
}); 