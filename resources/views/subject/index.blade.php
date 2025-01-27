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
	@can('subjects_add')
	 <h6><a href="{{route('subjects.create')}}" class="btn btn-xs btn-info right">ADD Subject</a></h6>
	@endcan
      <h6>SUBJECTS<h6>
	 </div>
  </div>
       <div class="card">
			<div class="card-content">
			<h6>Filters<h6>
			<div class="row">
				</div>
				<div class="row">
				<div class="input-field col s4">
				<input type="text" id="subjectsname" name="subjectsname">
				<label for="icon_prefix">Name</label>
				</div>
				<div class="input-field col s4">
				<input type="text" id="course" name="course">
				<label for="icon_prefix">Course</label>
				</div>
				{{--<div class="input-field col s4">
				<input type="text" id="deleted_at" name="deleted_at">
				<label for="icon_telephone">Deleted At</label>
				</div>--}}
				<div class="input-field col s4">
				<input type="text" id="subjectscode" name="subjectscode">
				<label for="icon_telephone">Subject code</label>
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
          <h4 class="card-title">Subjects Tables</h4>
          <div class="row">
              <table id="designationTable">
                <thead>
                  <tr>
                    <th>ID</th>
					<th>Name</th>
                    <th>Course</th>
                    <th>Subject code</th>
					 <th>Is Science faculty</th>
					 <th>Is Commerce faculty</th>
					 <th>Is Arts faculty</th>
					 <th>Is Agricultre faculty</th>
					<th>Action</th>
                  </tr>
                </thead>
                <tbody>
				
                    @foreach ($subjects as $subjectss)
                   <tr id ="row1">
                    <td>{{@$subjectss->id}}</td>
					<td>{{@$subjectss->name}}</td>
                    <td>{{@$subjectss->course}}</td>
                    <td>{{@$subjectss->subject_code}}</td>
					<td>{{@$yes_no[@$subjectss->is_science_faculty]}}</td>
					<td>{{@$yes_no[@$subjectss->is_commerce_faculty]}}</td>
					<td>{{@$yes_no[@$subjectss->is_arts_faculty]}}</td>
					<td>{{@$yes_no[@$subjectss->is_agricultre_faculty]}}</td>
					<td>
					@can('subjects_deleted')
					  <div class="invoice-action">
					  @if(!empty($subjectss->deleted_at))
					  <a href="{{ route('subjectsactive',[$subjectss->id,1]) }}" class="invoice-action-edit lock-confirm" title="Are You Show UnDeleted">
					  <i class="material-icons" >lock</i>				  
                 	  @else
					  <a href="{{ route('subjectsactive',[$subjectss->id,0]) }}" class="invoice-action-edit lock-confirm" title="Are You Show Deleted">
				      <i class="material-icons">lock_open</i>
					  @endif
					  @endcan
					@can('subjects_edit')
					<a href="{{ route('subjects.edit',$subjectss->id) }}" class="invoice-action-edit">
					<i class="material-icons" title="Click here to Edit.">edit</i>
					</a>
				    @endcan
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
	<script src="{!! asset('public/app-assets/js/bladejs/subjects_details.js') !!}"></script> 
@endsection 


