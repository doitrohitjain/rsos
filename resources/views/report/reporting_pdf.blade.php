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
      <p class="caption mb-0"><h6>Master Queries <span style="margin-left: 80%;"><a href="{{route('reports.create')}}" class="btn btn-xs btn-info pull-right">ADD Master Queries</a></span>
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
			<div class="col s12">
			<table border="0" cellspacing="5" cellpadding="5">
           <tbody>
			<tr>
			 <td>Title</td>
            <td><input type="text" id="title" name="title"></td>
			 <td>Status</td>
			 <td><input type="text" id="status" name="status"></td>
			</tr>
    </tbody></table><br><br><br>
	</div>
                <table id="reportstable" class="table">
                <thead>
				





				 
                </thead>
                <tbody>
                 
                </tfoot>
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
@endsection
 


