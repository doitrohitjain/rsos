
$('.tablinks').on("click", function() {
	var tabid = $(this).attr("data-tabid");
	if(tabid!=''){
		$('.tabcontent').hide();
		$('#'+tabid).show();
	}
});
