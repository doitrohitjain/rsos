// <script type="text/javascript">
	// jQuery Form Validator
//<script type="text/javascript">
	$(function() {
		$( ".from_date").datepicker({
			dateFormat : "dd-mm-yy",
			maxdate: '-14Y',
		});
		
		$( ".to_date").datepicker({
			dateFormat : "dd-mm-yy",
			maxdate: '-14Y',
		});
	});
	
	
    $(document).ready(function() { 
		// formId = "#" + config.data.formId; 
		formId = "#ExamLateFeeDate";
		$(formId).validate({
			rules: {
				 stream: { required: true,digits: true},
				 gender_id: { required: true,digits: true},
				 is_supplementary: { required: true,digits: true},
				 from_date: { required: true,date:true, },
				 to_date: { required: true,date:true, },
				 late_fee: { required: true,digits:true, maxlength:6},
			},
			errorElement: 'div', 
			messages: {
				stream: { 
					required: "Stream is required", 
				},
				gender_id: { 
					required: "Gender is required", 
				},
				is_supplementary: { 
					required: "Please select Supplementary", 
				},
				from_date: { 
					required: "From Date is required", 
				},
				to_date: { 
					required: "To Date is required", 
				},
				late_fee: { 
					required: "The Late Fee is required", 
				},
			},
			success: function(response){
			},
			submitHandler: function(form) {
				var clsName = "api";
				if($(formId).hasClass(clsName)){
					return true;
				} else {
					event.preventDefault();
					swal({
						title: 'Are you sure save your information.',
						text: "",
						icon: 'success',
						buttons: true,
					})
					.then((willsave) => {
						if (willsave) {
							$(formId).addClass(clsName); 
							$(formId).submit();
						}
					});
				}
			}
		});
	});
	
	
    $(document).ready(function(){
		minDate = new DateTime($('#min'), {
			format: 'MMMM Do YYYY'
		});
		maxDate = new DateTime($('#max'), {
			format: 'MMMM Do YYYY'
		});
			var table = $('#designationTable').DataTable({ 
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



