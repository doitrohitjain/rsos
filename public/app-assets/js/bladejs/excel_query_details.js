var table = $('#reportstable').DataTable({ 
          dom: 'Bfrtip', 
			 buttons: [
				'pageLength',
                'copy',
                'excel',
                'csv',
                'pdf'
	 ],
            });
// Event listener to the two range filtering inputs to redraw on input
        $('#transcript').keyup(function () {
            table.search( this.value ).draw();
        });
        $('#status').keyup('change', function () {
           table.columns(1).search( $(this).val(), false, false, false).draw();
        });
		 $('#title').keyup('change', function () {
           table.columns(5).search( $(this).val(), false, false, false).draw();
        });

        $('#excel').keyup('change', function () {
            table.columns(2).search( $(this).val(), false, false, false).draw();
         });

         $('#pdf').keyup('change', function () {
            table.columns(3).search( $(this).val(), false, false, false).draw();
         });
         $('#serialnumber').keyup('change', function () {
            table.columns(0).search( $(this).val(), false, false, false).draw();
         });
         $('#text').keyup('change', function () {
            table.columns(4).search( $(this).val(), false, false, false).draw();
         });
	
$('body').on('click', '.deleteProduct', function (){
    var baseUrl = './reports';    
	product_id = $(this).data("id");
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       },
                    type: "DELETE",
                    url: baseUrl+'/'+product_id,
                    success: function (data) {
						 $("#row1").remove();
						toastr.success(data.success);
                        location.reload();
                      },
                    error: function (data) {
						toastr.error(data.success);
                        console.log('Error:', data);
                    }

                });
             
        }

    });
});






// </script>



