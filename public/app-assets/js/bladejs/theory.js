$(document).ready(function() {
    $('.course_id').on('change',function(){
        var courseid = this.value;
        $(".subject_id").html('<option value="">Select Subject</option>');
    $.ajax({
              url: config.routes.getsubjects,
              type: "get",
              data: {'id': courseid},
              dataType : 'json',
              success: function (result){
                $(".subject_id").html('<option value="">Select Subject</option>');
                $.each(result,function(key,value){
                  $('.subject_id').append('<option value="' + key + '">' +value+'</option>');
                  $(".subject_id").trigger('contentChanged');
                });	
              },
            });
       
       });
});