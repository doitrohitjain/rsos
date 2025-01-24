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
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
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
							<h6><a href="{{ route('marking_absents')}}" class="btn btn-xs btn-info right">back</a></h6> 
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
										<th>Exam Center</th>
										<td>{{@$examiner_list[@$data->examcenter_detail_id]}}</td>
										<th>Course</th>
										<td>{{@$course_dropdown[$data->course_id]}}</td>
										<th  colspan='2'>Subject</th>
										<td  colspan='2'>{{@$data->subject_name}}</td>
									</tr>
									<tr>	
										<th>Total Students Appearing</th>
										<td>{{@$data->total_students_appearing}}</td>
										<th>Total Copies of the subject</th>
										<td colspan='2'>{{@$data->total_copies_of_subject}}</td>
										<th>Total Absent</th>
										<td>{{@$data->total_absent}}</td>
										
									</tr>
									<tr>	
										<th>Total NR</th>
										<td>{{@$data->total_nr}}</td>
									</tr>
									
								</tbody>
							</table>		
						</div>
						<br>
						<div class="row">
						<table class="responsive-table">
							<thead>
								<tr>
									<th>S/R</th>
									<!--<th>Fixcode</th>-->
									<th>Enrollment</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody> 
							   @php $count=1;
							   @endphp
								@if(@$result_student_list->count(0))
									@foreach($result_student_list as $dataa)
									@php
									$highlight_row_class = ($count % 2 != 0)? "highlight-row":'';  
							       @endphp
										<tr>
											<td  align="left" class="@php echo $highlight_row_class; @endphp">{{@$count}}</td>
											<!--<td  align="left" class="highlight-coloumn">{{@$dataa->fixcode}}</td>-->
											<td  align="left" class="highlight-coloumn">{{@$dataa->enrollment}}</td>
											<td  align="left" class="highlight-coloumn">
												@php
												 $absent_nr_status = ThoeryCustomComponent::getAbsentStudent(@$data->examcenter_detail_id,$data->course_id,@$data->subject_id,@$dataa->fixcode);
												
												
												@endphp
												@if(isset($absent_nr_status) && !empty($absent_nr_status) && @$absent_nr_status->theory_absent==1)
												<span style="color:red">Absent</span>
												@elseif(isset($absent_nr_status) && !empty($absent_nr_status) && @$absent_nr_status->theory_absent==2)
												<span style="color:red">NR</span>
												@else
												<span style="color:black">Present</span>
												@endif
											</td>

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




