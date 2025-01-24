@extends('layouts.default')
@section('content')
 <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
	 <h6><a href="{{route('roles.index')}}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Roles<h6>
	 </div>
  </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
		<table id="roleTable">
                <thead>
					<tr>
					<th width="450px">ID</th>
					<th width="450px">Name</th>
					<th>Permissions</th>
					</tr>
                </thead>
                <tbody>
                 <tr>
							<td> {{ $role->id }}</td>
							<td> {{ $role->name }}</td>
							<td>
							@if(!empty($rolePermissions))
							@foreach($rolePermissions as $v)
							<label class="label label-success">{{ $v->name }},</label>
							@endforeach
							@endif
							</td>
                    </tr> 
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
@endsection



