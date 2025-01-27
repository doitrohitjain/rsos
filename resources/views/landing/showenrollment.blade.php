@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span></span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						
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
					@if($type == 3)
					<h6><a href="{{route('updateiseligible',1)}}" class="btn btn-xs btn-info right mb-2 mr-1">all female is_eligible</a></h6>
				    @endif
					@if($type == 2)
					<h6><a href="{{route('updateiseligible',2)}}" class="btn btn-xs btn-info right mb-2 mr-1">all is_eligible</a></h6>
				    @endif
					<h6>Users Details<h6>
					
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
									@if($type == 1 || $type == 2)
									<thead>
									<tr>
									<th>Enrollment</th>
									<th>student</th>
									<th>application</th>
									<th>bankdetails</th>
									<th>documents</th>
									<th>admissionsubjects</th>
									<th>toc</th>
									<th>tocmarks</th>
									<th>examsubjects</th>
									<th>studentfees</th>
									</tr>
									</thead>
									@elseif($type == 3)
									<thead>
									<tr>
									<th>Enrollment</th>
									</tr>
									</thead>
									@endif
									<tbody>
									@foreach($getdata as $user)
									@if($type == 1 || $type == 2)
									<tr>
									@php
									$student1 = count((array)$user->student); 
									$application1 = count((array)$user->application);
									$bankdetails1 = count((array)$user->bank_details);
									$documents1 = count((array)$user->documents);
									$admissionsubjects1 = count((array)$user->admission_subjects);
									$toc1 = count((array)$user->toc);
									$tocmarks1 = count((array)$user->toc_marks);
									$examsubjects1 = count((array)$user->exam_subjects);
									$studentfees1 = count((array)$user->student_fees);@endphp
									@if($user->tocyesandnot == 1 && ($student1 == 0 || $application1 == 0 || $bankdetails1 == 0 || $documents1 == 0 || $admissionsubjects1 == 0|| $toc1 == 0 || $tocmarks1 == 0 || $examsubjects1 == 0 || $studentfees1 == 0 ))
									<td>{{$user->enrollment}}</td>
								    <td>{{count((array)@$user->student)}}</td>
									<td>{{count((array)@$user->application)}}</td>
									<td>{{count((array)@$user->bank_details)}}</td>
									<td>{{count((array)@$user->documents)}}</td>
									<td>{{count((array)@$user->admission_subjects)}}</td>
									<td>{{count((array)@$user->toc)}}</td>
									<td>{{count((array)@$user->toc_marks)}}</td>
									<td>{{count((array)@$user->exam_subjects)}}</td>
									<td>{{count((array)@$user->student_fees)}}</td>
                                    @elseif($student1 == 0 || $application1 == 0 || $bankdetails1 == 0 || $documents1 == 0 || $admissionsubjects1 == 0|| $toc1 == 0 || $tocmarks1 == 0 || $examsubjects1 == 0 || $studentfees1 == 0 )
									<td >{{$user->enrollment}}</td>
								    <td>{{count((array)@$user->student)}}</td>
									<td>{{count((array)@$user->application)}}</td>
									<td>{{count((array)@$user->bank_details)}}</td>
									<td>{{count((array)@$user->documents)}}</td>
									<td>{{count((array)@$user->admission_subjects)}}</td>
									<td> NO Toc</td>
									<td> NO Toc</td>
									<td>{{count((array)@$user->exam_subjects)}}</td>
									<td>{{count((array)@$user->student_fees)}}</td>
								    @endif
									</tr>
									@elseif($type == 3)
									<td>{{$user->enrollment}}</td>
									<td><a href="{{route('updateiseligible',1)}}" class="btn btn-xs btn-info">all female is_eligible</a></td >
									@endif
									@endforeach  
									</tfoot>
									</table>
									{{@$getdata->links('elements.paginater')}}
									
									
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
<script>
$(function() {

    $('#selectAll').click(function() {
        if ($(this).prop('checked')) {
            $('.your_checkbox_class').prop('checked', true);
        } else {
            $('.your_checkbox_class').prop('checked', false);
        }
    });

});
</script>



