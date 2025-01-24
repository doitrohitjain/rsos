var table = $('#reportsdatatable').DataTable({ 
          dom: 'Bfrtip',
			"search": {
			"caseInsensitive": true
			},		  
			 buttons: [
				
               
	 ],
            });
// Event listener to the two range filtering inputs to redraw on input

        $('#transcript').keyup(function () {
            table.search( this.value ).draw();
        });
       
		 $('#title').keyup('change', function () {
			table.columns(1).search().draw();
           
        });

       
         $('#serialnumber').keyup('change', function () {
            table.columns(0).search( $(this).val(), false, false, false).draw();
         });
         $('#text').keyup('change', function () {
            table.columns(4).search( $(this).val(), false, false, false).draw();
         });
        