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
												<tr><th>S.NO</th>
													<th>Enrollment</th>
													<th>Ai Code</th>
													<th>Name</th>
													<th>Marksheet Type</th>
													<th>Document Type</th>
													<th>lock Submitted status</th>
													<th>Total Fees</th>
													<th>Fee Paid Status</th>
													<th>Correction  Update</th>
													<th>Marksheet/Migration Download</th>
													<th>Action<th>
												</tr>
											</thead>
									<tbody> 
									@php $count=1; @endphp
									@if(!empty(@$master) && @$master->count(0))   
									@foreach ($master as  $item) 
										<tr>
										    <td>{{$count++}}</td>
											<td>{{$item->enrollment}}</td>
											<td>{{$item->ai_code}}</td>
											<td>{{$item->name}}</td>
											<td>{{@$marsheet_type[@$item->marksheet_type]}}</td>
											<td>{{@$document_type[$item->document_type]}}</td>
											<td>{{@$yes_no_temp[@$item->locksumbitted]}}</td>
											<td>{{$item->total_fees}}</td>
											<td>{{@$yes_no_temp[$item->fee_status]}}</td>
											<td>{{@$yes_no[$item->correction_update]}}</td>
											<td>{{@$yes_no[$item->marksheet_migration_status]}}</td>
											<td>
											@php
												$mmrid = Crypt::encrypt($item->mmrid);
											@endphp
											@if($item->fee_status == 1)
												@if(@$item->marksheet_type == 1)
												<a href="{{route('corr_marksheet_previews',$mmrid)}}"><i title="View Student form"  class="material-icons">remove_red_eye</i></a>
												<br>
												@can('result_update')
												<a href="{{route('updateindex',Crypt::encrypt($item->enrollment))}}" class="btn gradient-45deg-indigo-purple">
													<span>Update&nbsp;Marks</span>
												</a>
												<br><br>
												@endcan
												
												@if($item->correction_update != 1)
												<a href="{{ route('printupdatestudentdetalis',Crypt::encrypt($item->student_id)) }}" class="btn gradient-45deg-indigo-purple">
													<span>update&nbsp;student</span>
												</a>
												<br>
												@endif
												@endif
												@if($item->marksheet_type == 2 || (@$item->marksheet_type==1 &&@$item->correction_update==1))
											<a href="{{ route('printduplicatemarksheetcertificate',Crypt::encrypt($item->enrollment)) }}" class="btn gradient-45deg-indigo-purple">
												<span>Print&nbsp;Marksheet</span>
											</a>
											@endif
											@endif
											</td>
											 
										</tr>
									@endforeach 
                                    @php  $count++; @endphp
										@else
											<tr>
												<td colspan="10" class="text-center text-primary">No data found</td>
											</tr>
										@endif
									</tbody>
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