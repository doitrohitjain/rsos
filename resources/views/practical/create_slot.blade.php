@extends('layouts.default')
@section('content')
<?php
$mytime = Carbon\Carbon::now();

?>
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
        </div> 
		
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if ($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										</strong>
									</span>
										<div class="blue-text">
										 {{@$message}}
										</div>
									<h6>{{ $title }}
									 <div class="step-actions right">
										  <div class="row">
											<div class="col m8 s10 mb-2">
											
											@if($isAllowDate == true)
											@if(@$message != null)
											@can('PRATICAL_EXAMINER_SLOT')	
											<a href="{{route('createpracticalexaminerslot',encrypt($user_examiner_map_id))}}" class="btn btn-xs btn-info right">Create Slot</a>
											@endcan
											@endif
											@endif
											</div>
											<div class="col m4 s12 mb-2">
											 <a href="{{route('practicals')}}" class="btn btn-xs btn-info right">Back</a>
											</div>
										  </div>
										</div>
									</h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<div class="row">
			
		</div> 
       <!-- <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									
								</div>
							</div>
						</div>
					</div>
				</div>
-->
				<div class="section section-data-tables"> 
			<div class="row">
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							<div class="row"> 
								<table>
									<thead>
										<tr>
											<th><center>S.No.</center></th>
											<th><center>Exam Center</center></th>
											<th><center>Course</center></th>
											<th><center>Subject Name</center></th>
											<th><center>Start Date Time</center></th>
											<th><center>End Date Time</center></th>
											<th><center>Batch Student Count</center></th>
											<th><center>Slot Lock & Submit</center></th>
											<th><center>Action</center></th>
											</tr>
									</thead>
									<tbody>
										@php  $i= 1; @endphp
										@foreach(@$master as $data)
										
										<tr>
										<td><center>{{$i}}</center></td>
										<td><center>{{@$examcenter_list[@$data->examcenter_detail_id]}}</center></td>
										<td><center>{{$course_list[@$data->course]}}</center></td>
										<td><center>{{$subject_list[@$data->subject_id]}}</center></td>
										<td><center>{{@date('d-m-Y h:i A',strtotime(@$data->date_time_start))}}</center></td>
										<td><center>{{@date('d-m-Y h:i A',strtotime(@$data->date_time_end))}}</center></td>
										<td><center>{{@$data->batch_student_count}}</center></td>
										<td><center>{{$yes_no[@$data->entry_done]}}</center></td>
										<td><center>
										@can('delete-slot')
											<a href="{{route('deleteSlot',encrypt($data->id))}}" class=""><i class="material-icons" title="Click here to Delete.">delete</i></a> 
										@endcan	
											<a href="{{route('viewSlotData',encrypt($data->id))}}" class=""><i class="material-icons" title="
											View Slot">remove_red_eye</i></a> 
											
											@if($data->date_time_start <  $mytime->toDateTimeString() &&
										$data->date_time_end > $mytime->toDateTimeString() && $data->entry_done == 0)
										    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="35" alt="materialize logo"/>
											<a href="{{route('add_marks',encrypt($data->examiner_mapping_id))}}" class="btn btn-xs btn-info invoice-action-view" title="Click here to Entry Marks." style="font-weight:bold;">Practical Marks Entry</a>
										@endif
										</center></td>
										
										</tr>	
										@php  $i++; @endphp
										@endforeach
									</tbody>
								</table>
								{{-- $master->links('elements.paginater') --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> 
		</div>
    </div>
</div>
<div>
@endsection

@section('customjs')
	
	<script src="{!! asset('public/app-assets/js/bladejs/practical/create_slot.js') !!}"></script> 
@endsection 