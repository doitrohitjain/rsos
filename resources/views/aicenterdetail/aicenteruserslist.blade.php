@extends('layouts.default')
@section('content')
@if(!empty(@$id = Auth::user()->id))
	<div id="main">
		<div class="row">
		@endif
        <!-- <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			Search for small screen
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
        </div> -->
		
        {{-- removed col s12 --}}
		<div class="">
			<div class="container"> 
				<div class="section section-data-tables"> 
				<div class="row">
				<div class="col s12">
					<div class="card">
					<div class="card-content">
					
					 <h6><a href="{{route('landing')}}" class="btn btn-xs btn-info right mb-2 mr-1">Back to Home</a></h6>
					  
					<h6><b>{{ @$title }}</b><h6>
					</div>
				</div>
				</div>
				
				
				<div class="row">
					<div class="col s12">
						<div class="card3">
							<div class="card-content3 customAnimate">
								@include('elements.aicenter_listing_notifications')
							</div>
						</div>
					</div>
				</div>
				
				@if(@$showPopup && $showPopup == true)
					<div class="row">
						<div class="col s12">
							<div class="card3">
								<div class="card-content3"> 
									@include('elements.aicenter_notification_popup') 
								</div>
							</div>
						</div>
					</div>
				@endif
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							@include('elements.filters.search_filter')
						</div>
					</div>
				</div>
				<div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
              <table id="designationTable">
                <thead>
					<th style="width:5%;">SR.NO</th>
					<th style="width:5%;">Ai Code</th> 
					<th style="width:35%;">AI Centres Name</th>
					<th style="width:5%;">District</th>
					<th style="width:5%;">Block</th>
					<th style="width:10%;">Nodal Officer Name</th>
					<th style="width:13%;">Nodal  Officer Mobile 
                </thead>
                <tbody>
                     @foreach ($data as $key => $user) 
					<tr>
					<td style="width:5%;">{{ @$i++ }}</td>
					<td style="width:10%;"> {{ @$user->ai_code }}</td>
					<td style="width:35%;"> {{ @$user->college_name }}</td>
					<td style="width:10%;">{{ @$district_list[$user->temp_district_id] }}</td>
					<td style="width:10%;">{{ @$master_block_list[$user->temp_block_id] }}</td>
					<td style="width:10%;" >{{ @$user->nodal_officer_name }}</td>
					<td style="width:10%;" >{{ @$user->nodal_officer_mobile_number }}</td> 
					</tr>
					@endforeach    
                  </tr>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div> 

@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/aicenteruserlist.js') !!}"></script> 
@endsection  
 