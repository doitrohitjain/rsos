@extends('layouts.default')
@section('content')
 <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>DataTable</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Table</a>
                  </li>
                  <li class="breadcrumb-item active">DataTable
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="col s12">
          <div class="container">
            <div class="section section-data-tables">
          <div class="card">
		@can('examdate_add')
			<div class="card-content">
	 <h6><a href="{{ route('examdateadd') }}" class="btn btn-xs btn-info right">ADD Late Fee Dates</a></h6>
      <h6>Exam Late Fee Dates<h6>
	 </div>
	   @endcan
  </div>
       
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <h4 class="card-title">Exam Dates</h4>
          <div class="row">
              <table id="designationTable">
                <thead>
                  <tr>
                    <th>ID</th>
					<th>Stream</th>
                    <th>Gender</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Late Fee</th>
                    <th>IS For Supplementary</th>
                    <th>Extra Late Fee Allowed Days</th>
                    <th>Ordering</th>
					<th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($exam_late_dates_arr as $exam_late_dates)
                  <tr id ="row1">
                    <td>{{@$exam_late_dates->id}}</td>
					<td>{{@$stream_arr[$exam_late_dates->stream]}}</td>
                    <td>{{@$gender_arr[$exam_late_dates->gender_id]}}</td>
                    <td>{{@$exam_late_dates->from_date}}</td>
                    <td>{{@$exam_late_dates->to_date}}</td>
                    <td>{{@$exam_late_dates->late_fee}}</td>
                    <td>
                        @if(@$yes_no[$exam_late_dates->is_supplementary])
							@php echo @$yes_no[$exam_late_dates->is_supplementary]; @endphp
						@else
							@php echo @$yes_no[$exam_late_dates->is_supplementary]; @endphp
						@endif
                    </td>
                    <td>{{@$exam_late_dates->latefee_extra_days}}</td>
                    
                    <td>{{@$exam_late_dates->ordering}}</td>
					<td>
					  <div class="invoice-action">
					  {{--<a href="app-invoice-view.html" class="invoice-action-view mr-4">
					  <i class="material-icons" title="Click here to View.">remove_red_eye</i></a>--}}
					  @can('examdate_add')
					  <a href=" {{ route('examdateedit',$exam_late_dates->id) }}" class="invoice-action-edit">
					  <i class="material-icons"title="Click here to Edit.">edit</i></a>
					  @endcan
					  @can('examdate_delete')
					   <a href="javascript:void(0)" class="invoice-action-delete delete_dates" data-id="{{@$exam_late_dates->id}}">
					  <i class="material-icons" title="Click here to Delete.">delete</i></a>
					   @endcan
					  </div>
					  </td>
                    </tr>
                   @endforeach   
                  </tr>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- END RIGHT SIDEBAR NAV -->

          </div>
          <div class="content-overlay"></div>
        </div>
    
    </div>
	</div>
	</div>
@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/late_fee_dates.js') !!}"></script> 
@endsection 


