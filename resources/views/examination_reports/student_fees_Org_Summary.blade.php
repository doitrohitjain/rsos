@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
									<th>Sr no</th>
									<th>College Name</th>
									<th>AiCode</th>
									<th>Number Of Student</th>
									<th>Registration Fees</th>
									<th>Services Fees</th>
									<th>ADD Subject Fees</th>
									<th>Forward Fees</th>
									<th>Toc Fees</th>
									<th>Practical Fees </th>
									<th>Readm Exam Fees</th>
									<th>Late Fees</th>
									<th>Total</th>
									<th>Org Registration Fees</th>
									<th>Org Services Fees</th>
									<th>Org ADD Subject Fees</th>
									<th>Org Forward Fees</th>
									<th>Org Toc Fees</th>
									<th>Org Practical Fees </th>
									<th>Org Readm Exam Fees</th>
									<th>Late Fees</th>
									<th>Org Total</th>
									
									</tr>
									</thead>
									<tbody>
									@php $i =1 ; @endphp
									@foreach ($master as $key => $user)
									<tr>
									<td>{{ @$i++ }}</td>
									<td>{{ @$user->college_name }}</td>
									<td>{{ @$user->ai_code }}</td>
									<td>
										@php $fld = "number_of_student"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "registration_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "online_services_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "add_sub_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "forward_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "toc_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "practical_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "readm_exam_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td> 
									<td>
										@php $fld = "late_fee"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td> 
									<td>
										@php $fld = "total"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td> 
									<td>
										@php $fld = "org_registration_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_online_services_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_add_sub_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_forward_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_org_toc_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_practical_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									<td>
										@php $fld = "org_readm_exam_fees"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td> 
									<td>
										@php $fld = "org_late_fee"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td> 
									<td>
										@php $fld = "org_total"; @endphp
										@if(@$user->$fld > 0)
											@php echo @$user->$fld ; @endphp
										@else
											@php echo "0"; @endphp
										@endif
									</td>
									</tr>
									@endforeach  
									@php $i++; @endphp
									</tfoot>
									</table>
									{{ $master->links('elements.paginater') }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_student_application.js') !!}"></script> 
@endsection 


