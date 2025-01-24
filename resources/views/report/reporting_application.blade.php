@extends('layouts.default')
@section('content')
<div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
    <div class="card-content">
     
<h6></p>
    </div>
  </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <h4 class="card-title">Table Filters</h4>
          <div class="row">
            <div class="col s12">
			@include('elements.filters.filterkayvalue')
                <table id="data-table">
                <thead>
                  <tr>
                    <th>ID</th>
					<th>jan_aadhar_number</th>
                    <th>Date</th>
                  </tr>
                </thead>
				<tbody>
                  @foreach ($applicationsfilter as $applicationsfilters)
                  <tr id ="row1">
                    <td>{{@$applicationsfilters->id}}</td>
					<td>{{@$applicationsfilters->jan_aadhar_number}}</td>
                    <td>{{@$applicationsfilters->created_at}}</td>
                    </tr>
                   @endforeach  
                </tfoot>
              </table>
              </table>
            </div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_application_details.js') !!}"></script> 
@endsection 
