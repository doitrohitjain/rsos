$(document).ready(function() {
    $('.module_id').on('change',function(){
        var module_id = this.value;
        $(".id").html('<option value="">Select Sub Module</option>');
    $.ajax({
		  url: config.routes.getSubModuleList,
		  type: "get",
		  data: {'module_id': module_id},
		  dataType : 'json',
		  success: function (result){
			$(".id").html('<option value="">Select Sub Module</option>');
			$.each(result,function(key,value){
			  $('.id').append('<option value="' + key + '">' +value+'</option>');
			  $(".id").trigger('contentChanged');
			});	
		  },
		});
   
	});
   
    $('.collapsible').collapsible({
		accordion:false 
	});
		
});



$('.dataform').on('submit', function(e) {
    e.preventDefault(); 

    $.ajax({
        type: "POST",
        url: config.routes.update_ajax_single_screen_details,
        data: $(this).serialize(),
        success: function(result) {
        var status=result.status;
		var error=result.error;

            if(status == true){
              swal({
					title: "Your information has been successfully updated.",
					text: "",
					icon: "success",
					button: "Close",
					timer: 30000
				}).then((result) => {
					return false;
				})
			}else{
			 swal({
					title: "Something is Wrong.",
					text: error,
					icon: "error",
					button: "Close",
					timer: 30000
				}).then((result) => {
					return false;
				})
			}
        }
    });
});