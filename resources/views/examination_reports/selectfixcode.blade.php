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
      <div id="tap-target" class="card card-tabs">
        <div class="card-content">
             {!! Form::open(array('route' => 'selected_enrollment_fixcode_view_bulk','method'=>'POST')) !!}
            <div class="row">
            <div class="col m6 s12">
                @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align course','placeholder' => $placeholder,'id'=>'course']) !!}
                @include('elements.field_error')
                </div>
                </div>

                <div class="col m6 s12">
                @php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream']) !!}
                @include('elements.field_error')
              </div>
              </div>

                <div class="col m12 s12">
                @php $lbl='Editer'; $placeholder = "Select ". $lbl; $fld='tempStudentIds1'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::textarea($fld,null, array('class'=>'form-control', 
                    'rows' => 50, 'cols' => 10, 'style' => 'height:15rem;')) !!}
                @include('elements.field_error')
            </div>
            </div>
          </div>
          
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Submit
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="{{route('selectfixcodewisedata')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
  
      </div>
	</div>
</div>
@endsection
 

