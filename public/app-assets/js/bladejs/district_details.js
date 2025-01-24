// <script type="text/javascript">
	// jQuery Form Validator
var minDate, maxDate;
 
// Custom filtering function which will search data in column four between two values
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = minDate.val();
        var max = maxDate.val();
        var date = new Date( data[4] );
 
        if (
            ( min === null && max === null ) ||
            ( min === null && date <= max ) ||
            ( min <= date   && max === null ) ||
            ( min <= date   && date <= max )
        ) {
            return true;
        }
        return false;
    }
);
    $(document).ready(function(){
	minDate = new DateTime($('#min'), {
        format: 'MMMM Do YYYY'
    });
    maxDate = new DateTime($('#max'), {
        format: 'MMMM Do YYYY'
    });
        var table = $('.designationTable').DataTable({ 
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
        $('#namedis').keyup('change', function () {
           table.columns(3).search( $(this).val(), false, false, false).draw();
        });
		$('#namemagnal').keyup('change', function () {
           table.columns(4).search( $(this).val(), false, false, false).draw();
        });
		$('#code').keyup('change', function () {
           table.columns(2).search( $(this).val(), false, false, false).draw();
        });
        $('#Statename').keyup('change', function () {
           table.columns(1).search( $(this).val(), false, false, false).draw();
        });
        
    });
$('body').on('click', '.deleteProduct', function (){
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
                    url: "{{ route('districts.index') }}"+'/'+product_id,
                    success: function (data) {
						 $("#row1").remove();
						toastr.success(data.success);
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



