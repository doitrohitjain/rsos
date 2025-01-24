// <script type="text/javascript">
	// jQuery Form Validator

// Custom filtering function which will search data in column four between two values

    $(document).ready(function(){
        var table = $('#reportstable').DataTable({ 
          dom: 'Bfrtip',
          responsive: true,          
			 buttons: [
				'pageLength',
                'copy',
                'excel',
                'csv',
                'pdf'
	 ],
            });

    // Event listener to the two range filtering inputs to redraw on input
      
		$('#text').keyup('change', function () {
           table.columns(1).search( $(this).val(), false, false, false).draw();
        });
        $('#title').keyup('change', function () {
           table.columns(3).search( $(this).val(), false, false, false).draw();
        });
        $('#document').keyup('change', function () {
           table.columns(7).search( $(this).val(), false, false, false).draw();
        });
        $('#status').keyup('change', function () {
            table.columns(2).search( $(this).val(), false, false, false).draw();
         });
         $('#link').keyup('change', function () {
            table.columns(6).search( $(this).val(), false, false, false).draw();
         });
         $('#doctype').keyup('change', function () {
            table.columns(4).search( $(this).val(), false, false, false).draw();
         });
         $('#serial_number').keyup('change', function () {
            table.columns(0).search( $(this).val(), false, false, false).draw();
         });

        
    });
$('body').on('click', '.deleteProduct', function (){
	product_id = $(this).data("id");
    var baseUrl = './alldocumentdestory';
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



