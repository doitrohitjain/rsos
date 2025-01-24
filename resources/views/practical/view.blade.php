@extends('layouts.default')
@section('content')
@php
use App\Component\ThoeryCustomComponent;
@endphp
<style>

th, td {
	border:.1px solid rgba(0, 0, 0, 0.5);
}

.highlight-coloumn { background-color:#058ee921;}
.highlight-row { background-color:#3c8dbc30;}


</style>
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
							
						</ol>
					</div>
				</div>
			</div>
        </div>
    </div>
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="card">
						<div class="card-content">
						
							<h6><a href="{{ route('create_slot',encrypt($slotData->examiner_mapping_id))}}" class="btn btn-xs btn-info right">back</a></h6> 
							<h6>{{ $title }}</h6>
						</div>
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
							<table class="responsive-table" >
								<tbody>
									<tr >
									
										<th >Slot Start  Time</th>
										<td>{{@date('d-m-Y h:i A',strtotime($slotData->date_time_start))}}</td>
										<th>Slot End Time</th>
										
										<td>{{@date('d-m-Y h:i A',strtotime($slotData->date_time_end))}}</td>
										<th  colspan='2'> Batch Student Count</th>
										<td  colspan='2'>{{@$slotData->batch_student_count}}</td>
									</tr>
									
								</tbody>
							</table>		
						</div>
						<br>
						<div class="row">
						<table class="responsive-table">
							<thead>
								<tr>
									<th width="5%"><center>Sr.No.</center></th>
									<th width="24%"><center>Name</center></th>
									<th width="24%"><center>Enrollment</center></th>
									<th width="24%"><center>Student Lock & Submitted</center></th>
									<th width="23%"><center>Status</center></th>
									
								</tr>
							</thead>
							<tbody> 
							   @php $count=1;
							   @endphp
							   
								@if(@$data->count(0))
									
									@foreach($data as $dataa)
									@php
									$highlight_row_class = ($count % 2 != 0)? "highlight-row":'';  
							       @endphp
								   
										<tr>
											<td><center>{{$count}}</center></td>
											<td><center>{{$dataa->name}}</center></td>
											<td><center>{{@$dataa->enrollment}}</center></td>
											<td><center>{{$yes_no[$dataa->is_update_practical_marks_practical_examiner]}}</center></td>
										@if(@$dataa->practical_absent == 1)
											<td><center>Absent</center></td>
										@else
											<td><center>{{@$dataa->final_practical_marks}}</center></td>
										@endif

										@php  $count++; @endphp
									@endforeach  
								@else
									<tr>
										<td colspan="10"><h6 style="text-align:center; color:rgba(34, 188, 199, 0.918)">There are no data.</h6></td>
									</tr>
								@endif         
							</tbody>
						</table>
					</div>		
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div> 
@endsection