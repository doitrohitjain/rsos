@extends('layouts.default')
@section('content')
 <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Districts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Table</a>
                  </li>
                  <li class="breadcrumb-item active">Districts 
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
	 <h6><a href="{{route('districts.create')}}" class="btn btn-xs btn-info right">ADD District</a></h6>
      <h6>DISTRICTS<h6>
	 </div>
  </div>
       <div class="card">
			<div class="card-content">
			<h6>Filters<h6>
			<div class="row">
				</div>
				<div class="row">
				<div class="input-field col s4">
				<input type="text" id="Statename" name="Statename">
				<label for="icon_prefix">State name</label>
				</div>
				<div class="input-field col s4">
				<input type="text" id="code" name="code">
				<label for="icon_prefix">Code</label>
				</div>
				<div class="input-field col s4">
				<input type="text" id="namedis" name="namedis">
				<label for="icon_telephone">Name</label>
				</div>
				<div class="input-field col s4">
				<input type="text" id="namemagnal" name="namemagnal">
				<label for="icon_telephone">Name Mangal</label>
				</div>
				<div class="right">
                    @php
                        $route = Route::current()->getActionName();
					@endphp
					<a href="{{ action($route) }}" class="btn btn-primary">Reset</a></td>
				</div>
				</div>
				</div> </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <h4 class="card-title">Districts </h4>
          <div class="row">
              <table id="designationTable">
                <thead>
                  <tr>
                    <th>ID</th>
					<th>State name</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Name Mangal</th>
                    <th>Date</th>
					<th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($districts as $districtss)
                   <tr id ="row1">
                    <td>{{@$districtss->id}}</td>
					<td>{{@$districtss->state->name}}</td>
                    <td>{{@$districtss->code}}</td>
                    <td>{{@$districtss->name}}</td>
                    <td>{{@$districtss->name_mangal}}</td>
                    <td>{{@$districtss->created_at}}</td>
					<td>
					  <div class="invoice-action">
					  {{--<a href="app-invoice-view.html" class="invoice-action-view mr-4">
					  <i class="material-icons" title="Click here to View.">remove_red_eye</i></a>--}}
					  <a href=" {{ route('districts.edit',Crypt::encrypt($districtss->id)) }}" class="invoice-action-edit">
					  <i class="material-icons" title="Click here to Edit.">edit</i></a>
					   <a href="javascript:void(0)" class="invoice-action-delete deleteProduct" data-id="{{@$districtss->id}}">
					  <i class="material-icons" title="Click here to Delete.">delete</i></a>
					  </div>
					  </td>
                   @endforeach  
                  </tr>
              </table>
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
	</div>
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/district_details.js') !!}"></script> 
@endsection 


