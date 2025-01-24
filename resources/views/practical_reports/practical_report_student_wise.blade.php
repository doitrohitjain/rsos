@extends('layouts.default')
@section('content')
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
									@include('elements.filters.search_filter')
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row">								
									    <table>
											<thead>	
												<tr>
												<th>Sr. No</th>
												<th>Exam Center</th>
												<th>Course</th>
												<th>Subject Name</th>
												<th>Examiner SSO</th>
												<th>Batch Student Count	</th>
												<th>Start Date Time</th>
												<th>End Date Time</th>
												<th>Slot Lock & Submit</th>
												<th>Skip Slot</th>
												<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@php $count=1; @endphp
												@if(!empty(@$master) && @$master->count(0))   
												@foreach(@$master as $data)
											
													<tr>
													<td>{{$count++}}</td>
													<td>{{@$examCenterList[@$data->examcenter_detail_id]}}</td>
													<td>{{@$course[$data->course]}}</td>
													<td>{{$subject_list[@$data->subject_id]}}</td>
													<td>{{@$user_practical_examiner_id[@$data->user_practical_examiner_id]}}</td>
													<td>{{@$data->batch_student_count}}</td>
													
													<td>{{@date("d-m-Y H:i:s",strtotime($data->date_time_start))}}</td>
													<td>{{@date("d-m-Y H:i:s",strtotime($data->date_time_end))}}</td>
													<td>{{$yes_no[@$data->entry_done]}}</td>
													<td>{{$yes_no[@$data->skip_slot]}}</td>
													<td>
													 @can('delete-slot')
														<a href="{{ route('deleteSlot',encrypt($data->id)) }}" class="invoice-action-delete delete-confirm2">
														<i class="material-icons" title="Click here to Delete.">delete</i></a>
													 @endcan
													</td>
													
													</tr>
											    @endforeach
												@else
												<tr>
													<td colspan="10" class="text-center text-primary">No data found</td>
												</tr>
											@endif
											</body>
											</table>
											{{$master->links('elements.paginater')}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
          <div class="content-overlay"></div>
        </div>
		</div>
    </div>
</div> 
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/student_report.js') !!}"></script> 
@endsection  