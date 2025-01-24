var countDownDate = null;
if(endtime !== ""){
  countDownDate = new Date(endtime).getTime();
}else{
  countDownDate = currentwithaddingtenmin;
} 
var allowtosee = true;
var hours = 1; 

var current_ip = phpJsVarSet.extra.current_ip;
var whiteListMasterIps = phpJsVarSet.extra.whiteListMasterIps;
whiteListMasterIps = $.parseJSON(whiteListMasterIps); //convert to javascript array
current_ip = current_ip.replace('"','');current_ip = current_ip.replace('"','');

if($.inArray(current_ip, whiteListMasterIps) != -1) {
	 hours = 480;
}
customTimer(hours); 
function customTimer(hours=1){
	var x = setInterval(function() {
	var now = new Date().getTime();
	var distance = countDownDate - now;
	var minutes = Math.floor((distance % (1000 * 60 * 60 * hours)) / (1000 * 60));
	var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	//   document.getElementById("count_down").innerHTML =  minutes + "M " + seconds + "S ";
	document.getElementById("count_down_min").innerHTML =  minutes;
	document.getElementById("count_down_sec").innerHTML =  seconds;
	if(minutes == 1 && allowtosee === true){
		var logoutUrl = phpJsVarSet.routes.logoutUrl;
		var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
		var action = "setCountDownTimerDetails";
		var ajaxUrl = ajaxBaseUrl + action;	
		allowtosee = false;
		swal({
			title: 'Continue with current login?(वर्तमान लॉगिन के साथ जारी रखें?)',
			text: "Your login session is about to expire. You will be automatically logout. To staty logged in, Please select continue with the current login?(आपका वर्तमान लॉगिन समाप्त होने वाला है. आप स्वचालित रूप से लॉगआउट हो जायेंगे. लॉग इन रहने के लिए, कृपया वर्तमान लॉगिन के साथ जारी रखें का चयन करें?)", 
			icon: 'info',  
			buttons: [
				'Logout',
				'Continue with current login'
			],
			timer: 60000
		 })
		.then((willsave) => {
			if (willsave == true) {
				$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				});
				$.ajax({
					url: config.routes.setCountDownTimerDetails,
					type: "POST",
					dataType: "json",
					success: function (response) {
						swal.close();
						location.reload();
					}
				});
			}else if (willsave == null) {
				window.location.href = logoutUrl;
			}
			
		});
		return false;
	  }
	  if (distance < 0) {
		clearInterval(x);
		//document.getElementById("count_down").innerHTML = "EXPIRED";
	  }
	}, 1000);
}



