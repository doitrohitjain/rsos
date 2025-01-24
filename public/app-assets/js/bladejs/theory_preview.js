/*
$(document).ready(function() { 
	$('.delete-confirm').on('click', function (event) {
		event.preventDefault();
		// const url = $(this).attr('href');
		swal({
			title: 'Are you sure want yo save your information.?',
			text: 'This record and it`s details will be Active!',
			icon: 'success',
			buttons: ["Cancel", "Yes!"],
		}).then(function(value) {
			if (value) {
				window.location.href = "{{URL::to('therory_mark_submissions')}}";
			}
		});
	});
});
*/