@extends('layouts.default')
@section('content')
 <!-- BEGIN: SideNav-->
  <!-- BEGIN: Page Main-->
    <div id="main">
      <div class="row">
	    <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>क्वेरीज एडिटर (SQl Queries Editer)</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Master</a>
                  </li>
                  <li class="breadcrumb-item active">Master
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">SQL एडिटर (SQL Queries
		  	<a href="{{ route('queryeditermulti') }}">Editer</a>
			  ) {{ Form::open(['route' => 'queryediterget', 'method' => 'post']) }}
			    {!! Form::token() !!}
		     <input type="hidden" name="queryediter" id="getexcel" required>
		     <div class="w100">
			<button name="hostel1" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content right" id="button1" type="submit"  value="The Venetian">Download Excel</button>
		   </div>
		    {{ Form::close() }}
          {{ Form::open(['route' => 'queryediter', 'method' => 'post']) }}
          {!! Form::token() !!}
            <div class="row">
            </div>

            @php $lbl='टेबल का नाम (Table Name)'; $placeholder = "Select ". $lbl; $fld='tablename'; @endphp
              	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8> 
             	
               <textarea class="form-control" id="getvalue" rows="50" cols="10" style="height:20rem;" required="required" name="queryediter" autocomplete="off">@php echo @$query; @endphp </textarea>

			
					
					<br><br>
             <div class="row">
			   <div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
                    </div>
                <div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
			   {{ Form::close() }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/queryediter_details.js') !!}"></script> 
@endsection 







