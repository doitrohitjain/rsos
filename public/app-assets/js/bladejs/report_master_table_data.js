$('body').on('click', '.deleteItem', function (){
    var baseUrl = '../report_master/destory';    
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
                type: "GET",
                url: baseUrl +'/'+product_id,
                success: function (data) {
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