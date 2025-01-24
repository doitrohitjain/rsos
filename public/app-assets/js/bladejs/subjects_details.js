var table = $('#designationTable').DataTable({ 
          dom: 'Bfrtip', 
			 buttons: [
				'pageLength',
                'copy',
                'excel',
                'csv',
                'pdf'
	 ],
	 lengthMenu: [
            [100, 25, 50, -1],
            [100, 25, 50, 'All'],
        ],
            });
// Event listener to the two range filtering inputs to redraw on input
        $('#transcript').keyup(function () {
            table.search( this.value ).draw();
        });
        $('#subjectsname').keyup('change', function () {
           table.columns(1).search( $(this).val(), false, false, false).draw();
        });
		 $('#course').keyup('change', function () {
           table.columns(2).search( $(this).val(), false, false, false).draw();
        });
		 //$('#deleted_at').keyup('change', function () {
           //table.columns(3).search( $(this).val(), false, false, false).draw();
        //});
		 $('#subjectscode').keyup('change', function () {
           table.columns(3).search( $(this).val(), false, false, false).draw();
        });

$(function(){
	$(".show").hide();
	$('.changetype').change(function(){
    var value = $(this).val();
	if (value == "1"){
	$(".show").show();
	}else if (value == "0"){
	$(".show").hide();
	}
 
	});
});

$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxsubjectsDetilasValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
			
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxsubjectsDetilasValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response.isValid==false){
							message = JSON.stringify(response); 
							// var obj = JSON.parse(response);
							 
							var errStr = "";
							var counter = 1;
							$.each(response, function(index, elementOuter) {
								$.each(elementOuter, function(index, elementt) {  
									$.each(elementt, function(indexiDis, elementtiDis) { 
										errStr += "" + counter + " : " + elementtiDis  + "\n";
										counter++;
									});
								});
							});
								 
							swal({
								title: "Validation Error",
								text: errStr,
								icon: "error",
								button: "Close",
								timer: 30000
							});
							return false;
						} else {
							
							var clsName = "api";

							if($(formId).hasClass(clsName)){ 
								return true;
							}
							swal({
								title: 'Are you sure save your information.',
								//text: "You won't be able to revert this!",
								//text: form_edit_msg,
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



