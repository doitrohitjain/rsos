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
    <div class="card-content">
      <p class="caption mb-0"><h6>{{ $title; }} <span style="margin-left: 85%;"><a href="{{ route('practicalexaminer') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
  </div>
  <!-- Page Length Options -->
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
			<div class="row">

				<div class="input-field col s6">
					@php $lbl='एसएसओआईडी (SSOID)'; $fld='ssoid'; @endphp
					<h8>{!!Form::label($fld, $lbl) !!}</h8>
					<br>@php echo @$master->$fld @endphp 
				</div>

				<div class="input-field col s6">
					@php $lbl='पूरा नाम (Full Name)'; $fld='name'; @endphp
					<h8>{!!Form::label($fld, $lbl) !!}</h8>
					<br>@php echo @$master->$fld @endphp 
				</div>

				<div class="input-field col s6">
					@php $lbl='ईमेल (Email)'; $fld='email'; @endphp
					<h8>{!!Form::label($fld, $lbl) !!}</h8>
					<br>@php echo @$master->$fld @endphp 
				</div>

				<div class="input-field col s6">
					@php $lbl='मोबाइल(Mobile)'; $fld='mobile'; @endphp
					<h8>{!!Form::label($fld, $lbl) !!}</h8>
					<br>@php echo @$master->$fld @endphp 
				</div>

				<div class="input-field col s6">
					@php $lbl='विद्यालय का नाम(School Name)'; $fld='college_name'; @endphp
					<h8>{!!Form::label($fld, $lbl) !!}</h8>
					<br>@php echo @$master->$fld @endphp 
				</div> 
				 
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



