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
				<!-- <h6><a href="{{ route('students.index') }}" class="btn btn-xs btn-info right">Back</a></h6> -->
					<h6>Deactived Students Details<h6>
					</div>
					</div>
					</div>
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
									<th>No</th>
									<th>Name</th>
									<th>Enrollment</th>
									<th>Ai code </th>
									<th>Gender</th>
									<th>Dob</th>
									<th>Admission</th>
									<th>Stream</th>
									<th>Course</th>
									<th>Lock And Submitted</th>
									<th>Is Eligible</th>
									<th>challan Number</th>
									<th>submitted</th>
									<th>Fees Amount</th>
									<th>Deleted Date</th>
									<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach ($data as $key => $student)
									<tr>
									<td style="color:red">{{ @$student->id }}</td>
									<td style="color:red">{{ @$student->name }}</td>
									<td style="color:red">{{ @$student->enrollment  }}</td>
									<td style="color:red">{{ @$student->ai_code }}</td>
									<td style="color:red">{{ @$gender_id[$student->gender_id] }}</td>
									<td style="color:red">{{ @$student->dob }}</td>
									<td style="color:red">{{ @$adm_types[@$student->adm_type]}}</td>
									<td style="color:red">{{ @$stream_id[@$student->stream]}}</td>
									<td style="color:red">{{ @$student->course }}</td>
									<td style="color:red">{{ @$yes_no[@$student->application->locksumbitted]}}</td>
									<td style="color:red">{{ @$yes_no[@$student->is_eligible]}}</td>
									<td style="color:red">{{ @$student->challan_tid}}</td>
									<td style="color:red">{{ @$student->submitted}}</td>
									<td style="color:red">{{ @$student->application->fee_paid_amount}}</td>
									<td style="color:red">{{ $student->deleted_at}}</td>
									@can('deactivestudentactive')
									<td>
										<div class="invoice-action">
											<a href="{{ route('studentdeleteactive',Crypt::encrypt($student->id)) }}" class="btn btn-primary delete-confirm">
											Active
											</a>
										</div>
									</td>
									@endcan
                    </tr>
                   @endforeach   
									</tfoot>
									</table><br>
								{{ $data->links('elements.paginater') }}
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

@section('customjs')
	<script>
$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'Are you sure you want to re-active the selected student. It is showing in main students listing.',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
</script>
@endsection 



