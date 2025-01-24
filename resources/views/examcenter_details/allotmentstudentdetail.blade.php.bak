@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $page_title }}</span></h5>
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
					 <h6><a href="{{route('center_student_allotment_report_excel')}}" class="btn btn-xs btn-info right mb-2 mr-1  gradient-45deg-purple-deep-orange">Export Excel</a></h6>
			<h6>Export Excel<h6>
								
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
										<th>Ai Center</th>
										<th>Ai Code</th>
										 
										<th>Exam Center</th>
										
										<th>Eligible Fresh 10</th>
										<th>Eligible Fresh 12</th>
										<th>Eligible Supplementary 10</th>
										<th>Eligible Supplementary 12</th>
										
										<th>Eligible Total(fresh+supp) 10</th>
										<th>Eligible Total(fresh+supp) 12</th>
										<th>Eligible Total(fresh+supp) (10+12)</th>
										
										<th>Total Allotted(fresh+supp) 10</th>
										<th>Total Allotted(fresh+supp) 12</th>
										<th>Total Allotted(fresh+supp) (10+12)</th>
										
										
										<th>Total Reaming(fresh+supp) 10</th>
										<th>Total Reaming(fresh+supp) 12</th>
										<th>Total Reaming(fresh+supp) (10+12)</th>
									
									</tr>
									</thead>
									<tbody>
									@foreach($finalArr as $data)
									  
									<tr>
									<td>{{ @$data['other']['ai_name'] }}</td>
									<td>{{ @$data['other']['ai_code'] }}</td>
									<td>{{ @$data['other']['examcenters'] }}</td>
									<td>{{ @$data['student']['10'] }}</td>
									<td>{{ @$data['student']['12'] }}</td>
									<td>{{ @$data['supplementary']['10'] }}</td>
									<td>{{ @$data['supplementary']['12'] }}</td>
									<td>{{ @$data['total_student']['10'] }}</td>
									<td>{{ @$data['total_student']['12'] }}</td>
									<td>{{ @$data['total']}}</td>
									<td>{{ @$data['student_allotment']['10'] }}</td>
									<td>{{ @$data['student_allotment']['12'] }}</td>
									<td>{{ @$data['student_allotment']['10'] +  @$data['student_allotment']['12']}}</td>
									<td>{{ @$data['reaming_student_allotment']['10'] }}</td>
									<td>{{ @$data['reaming_student_allotment']['12'] }}</td>
									<td>{{ @$data['reaming_student_allotment']['10'] +  @$data['reaming_student_allotment']['12']}}</td>
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




