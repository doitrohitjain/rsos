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
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Master</span></h5>
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
          <h4 class="card-title">Form Multiple Queries Editor (seprated by '<b style='font-size:40px;'>:az:</b>') Allow only Insert/Update</h4>

          <h6>
            <b>Sample1 : </b> update rs_students set `password` = id where id = 1;:az:
                    update rs_students set `password` = id where id = 2;:az: 
                    update rs_students set `password` = id where id = 3;
          </h6>
          <h6>
		  <b>Sample2 : </b> : INSERT INTO `rs_students` (`id`, `is_eligible`) VALUES (2, 1);:az:
                    INSERT INTO `rs_students` (`id`, `is_eligible`) VALUES (2, 1);:az:
                    INSERT INTO `rs_students` (`id`, `is_eligible`) VALUES (2, 1);
          </h6>
          {{ Form::open(['route' => 'queryeditermulti', 'method' => 'post']) }}
          {!! Form::token() !!}
            <div class="row">
            </div>
			 {!! Form::label('Queries Editer', 'Queries Editer') !!}
             {!! Form::textarea('queryeditermulti',null, array('class'=>'form-control', 
                    'rows' => 50, 'cols' => 10, 'style' => 'height:20rem;')) !!}
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
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection







