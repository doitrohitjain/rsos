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
	 <h6><a href="{{route('roles.create')}}" class="btn btn-xs btn-info right">ADD Role</a></h6>
      <h6>Roles<h6>
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
                    <td class="col-md-3 right">
                    @php
                        $route = Route::current()->getActionName();
					@endphp
					<a href="{{ action($route) }}" class="btn btn-primary">Reset</a></td>

			</tr>
		</tbody></table>
		<br><br><br>
		<table id="roleTable">
                <thead>
					<tr>
					<th width="420px">ID</th>
					<th width="420px">Name</th>
					<th>Action</th>
					</tr>
                </thead>
                <tbody>
                 @foreach ($roles as $key => $role)
                 <tr id ="row1">
							<td>
							{{ $role->id }}
							</td>
							<td>{{ $role->name }}</td>
					        <td>
							<div class="invoice-action">
							<a href="{{ route('roles.show',$role->id) }}" class="invoice-action-view" title="Click here to View.">
							<i class="material-icons">remove_red_eye</i>
							</a>
							<a href="{{ route('roles.edit',$role->id) }}" class="invoice-action-edit" title="Click here to Edit.">
							<i class="material-icons">edit</i>
							</a>
							<a href="javascript:void(0)" class="invoice-action-delete deleteProduct" data-id="{{@$role->id}}">
							<i class="material-icons" title="Click here to Delete.">delete</i></a>
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
@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/role_details.js') !!}"></script> 
@endsection 


