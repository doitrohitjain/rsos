// <script type="text/javascript">
	// jQuery Form Validator
 $(document).ready(function(){
	 var table = $('#data-table').DataTable({ 
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
        $('#namedis').keyup('change', function () {
           table.columns(4).search( $(this).val(), false, false, false).draw();
        });
		$('#namemagnal').keyup('change', function () {
           table.columns(5).search( $(this).val(), false, false, false).draw();
        });
		$('#code').keyup('change', function () {
           table.columns(3).search( $(this).val(), false, false, false).draw();
        });
		$('#min, #max').keyup('change', function () {
          table.draw();
    });
        
    });

// </script>



