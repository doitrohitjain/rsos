 @include('layouts.appheader')
 @include('layouts.appleft')
 <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="public/app-assets/images/gallery/breadcrumb-bg.jpg">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>DataTable</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Table</a>
                  </li>
                  <li class="breadcrumb-item active">DataTable
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="col s12">
          <div class="container">
            <div class="section section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0"><h6>Colleges <span style="margin-left: 85%;"><a href="{{ route('colleges.create') }}" class="btn btn-xs btn-info pull-right">ADD Colleges</a></span>
<h6></p>
    </div>
  </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <h4 class="card-title">Table Filters</h4>
          <div class="row">
			<table border="0" cellspacing="5" cellpadding="5">
            <tbody>
			<tr class="col s12">
			 <td>Name</td>
             <td><input type="text" id="namedis" name="namedis"></td>
			 <td>Email</td>
             <td><input type="text" id="emaildi" name="emaildi"></td>
			</tr>
		</tbody></table><br><br><br>
		<table id="roleTable">
                <thead>
					<tr>
						<th>No</th>
						<th>Name</th>
						<th>Email</th>
						<th>Roles</th>
						<th>Action</th>
					</tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $college)
                 <tr id ="row1">
							<td>{{ $college->id }}</td>
							<td>{{ $college->name }}</td>
							<td>{{ $college->email }}</td>
							<td>
							@if(!empty($college->getRoleNames()))
							@foreach($college->getRoleNames() as $v)
							<label class="badge badge-success">{{ $v }}</label>
							@endforeach
							@endif
							</td>
					        <td>
							<div class="invoice-action">
							<a href="{{ route('colleges.show',$college->id) }}" class="invoice-action-view">
							<i class="material-icons">remove_red_eye</i>
							</a>
							<a href="{{ route('colleges.edit',$college->id) }}" class="invoice-action-edit">
							<i class="material-icons">edit</i>
							</a>
							<a href="javascript:void(0)" class="invoice-action-delete deleteProduct" data-id="{{@$college->id}}">
							<i class="material-icons">delete</i></a>
							</a>
							</div>
					  </td>
                    </tr>
                   @endforeach  
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- END RIGHT SIDEBAR NAV -->

          </div>
          <div class="content-overlay"></div>
        </div>
      </div>
    </div>
@include('layouts.centerseeting')
@include('layouts.appfooter')
<script type="text/javascript">
// Custom filtering function which will search data in column four between two values
   var table = $('#roleTable').DataTable({ 
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
           table.columns(1).search( $(this).val(), false, false, false).draw();
        });
		$('#emaildi').keyup('change', function () {
           table.columns(2).search( $(this).val(), false, false, false).draw();
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
                    url: "{{ route('colleges.index') }}"+'/'+product_id,
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
</script>


