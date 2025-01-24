 @include('layouts.appheader')
 @include('layouts.appleft')
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
    <div class="card-content">
      <p class="caption mb-0"><h6>Colleges <span style="margin-left: 85%;"><a href="{{ route('colleges.index') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
  </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
		<table id="roleTable">
                <thead>
					<tr>
						<th>No</th>
						<th>Name</th>
						<th>Email</th>
						<th>Roles</th>
					</tr>
                </thead>
                <tbody>
                 <tr>
							<td>{{ $college->id }}</td>
							<td>{{ $college->name }}</td>
							<td>{{ $college->email }}</td>
							<td>
							@if(!empty($college->getRoleNames()))
							@foreach($college->getRoleNames() as $v)
							<label class="badge badge-success">{{ $v }}</label>
							@endforeach
							@endif
							</td>
                    </tr> 
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
@include('layouts.centerseeting')
@include('layouts.appfooter')



