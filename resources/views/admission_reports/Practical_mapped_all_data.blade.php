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
									<table class="responsive-table">
									<thead>
									<tr>
									<th>Exam Center Name</th>
									<th>DEO SSOID</th>
									<th>Practical Examiner SSOID</th>
									<th>Course</th>
									<th>Subject Name</th>
									<th>Total Student Count</th>
									<th>Practical Examiner Not Assign Student Count</th>
									<th>Practical Examiner Assigned Student Count</th>
									<th>Practical Examiner Marks Entered</th>
									<th>Practical Examiner Not Marks Entered</th>
									<th>Pending Lock & Submit</th>
									<th>Complete Lock & Submit</th>
									<th>Is Dcoument Uploaded</th>
									<th>Is Practical Examiner Assigned By DEO</th>
									</tr>
									</thead>
									<tbody>
									@foreach ($data as $key => $user)
									<tr>
									<td >{{ @$user->Exam_Center11_Name }}</td>
									<td >{{ @$user->DEO_SSOID }}</td>
									<td >{{ @$user->Practical_Examiner_SSOID }}</td>
									<td >{{ @$user->Course }}</td>
									<td >{{ @$user->Subject_Name }}</td>
									<td >{{ @$user->Total_Student_Count }}</td>
									<td >{{ @$user->Practical_Examiner_Not_Assign_Student_Count }}</td>
									<td >{{ @$user->Practical_Examiner_Assigned_Student_Count }}</td>
									<td >{{ @$user->Practical_Examiner_Marks_Entered }}</td>
									<td >{{ @$user->Practical_Examiner_Not_Marks_Entered }}</td>
									<td >{{ @$user->Pending_Lock_Submit }}</td>
									<td >{{ @$user->Complete_Lock_Submit }}</td>
									<td >{{ @$user->Is_Dcoument_Uploaded }}</td>
									<td >{{ @$user->Is_Practical_Examiner_Assigned_By_DEO }}</td>
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
        </div>
		</div>
    </div>
</div> 
@endsection





