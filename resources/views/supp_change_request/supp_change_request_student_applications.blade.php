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
							<p class="caption mb-0"><h6>Supp Change Request Student Applications <span style="margin-left: 85%;"><a href="{{ route('examination_department') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
							<h6></p>
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
										<th>Sr.No.</th>
										<th>Enrollment</th> 
										<th>SSO</th>
										<th>AI Code</th>
										<th>Name</th>
										<th>Gender</th>
										<th>Course</th>
										<th>Stream</th>
										<th>Admission</th>
										<th>Lock Submitted</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@php $i = 1;@endphp
									@foreach ($master as $key => $user)
									<tr>
									 <td>{{ @$i }}</td>
									<td >{{ @$user->enrollment }}</td>
									 <td>{{ @$user->ssoid }}</td>
									<td >{{ @$user->ai_code }}</td>
									<td >{{ @$user->name }}</td>
									<td >{{ @$gender_id[@$user->gender_id] }}</td>
									<td >{{ @$course[@$user->course] }}</td>
									<td >{{ @$stream_id[@$user->stream] }}</td>
									<td >{{ @$adm_types[@$user->adm_type] }}</td>
										<td >@if($user->locksumbitted == 1)
										      {{'Yes'}}
										  @else
										  {{'NO'}}
										  @endif
									</td>
									@if($checkchangerequestsssupplementariesAllowOrNotAllow == true)
									@can('supp_student_change_requests_approved')
								    @if($user->supp_student_change_requests == 1 )
								    <td>
									 <div class="invoice-action">
									 <a href="{{ route('supp_student_change_requests_approveds',Crypt::encrypt($user->student_id)) }}" class="btn btn-primary delete-confirm">Approve</a>
									</div>
									</td>
									@elseif($user->supp_student_change_requests == 2 )
									<td>
									 <span class="badge green lighten-5 green-text text-accent-4">Approved</span>
									</td>
									@endif
									@endcan
									@endif
									</tr>
									 @php $i++; @endphp	
									@endforeach 								
									</tfoot>
									</table>
									{{ $master->withQueryString()->links('elements.paginater') }}
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
<script>
$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'You are going to allow student to update their application!',
        icon: 'warning',
        buttons: ["Cancel", "Yes"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
</script>
@endsection 




