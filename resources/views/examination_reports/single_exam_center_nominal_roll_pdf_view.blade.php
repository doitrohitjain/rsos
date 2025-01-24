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
        <br>
            <br>
      <div id="tap-target" class="card card-tabs">
        <div class="card-content">
             {!! Form::open(array('route' => 'single_exam_center_nominal_roll_pdf_request','method'=>'POST')) !!}
            <div class="row">
            <div class="col m3 s12">
                @php $lbl='पाठ्यक्रम(Course)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align course','placeholder' => $placeholder,'id'=>'course']) !!}
                @include('elements.field_error')
                </div>
                </div>

                <div class="col m3 s12">
                @php $lbl='स्ट्रीम(Stream)'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream']) !!}
                @include('elements.field_error')
            </div>
            </div>

                <div class="col m3 s12">
                @php $lbl='ज़िला(District)'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$district_list,null, ['class' => 'select2 browser-default form-control center-align district','placeholder' => $placeholder,'id'=>'district' ]) !!}

                @include('elements.field_error')
                </div>
                </div>
                <div class="col m3 s12">
                @php $lbl='परीक्षा केंद्र(Exam Center)'; $placeholder = "Select ". $lbl; $fld='ecenter'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align examcenter','placeholder' => $placeholder ,'id'=>'subjectstype']) !!}

                @include('elements.field_error')
                </div>
                </div>
          </div>
          
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Download
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="{{route('single_exam_center_nominal_roll_pdf_view')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
   @if($user_role == $developeradminrole)
		<div id="tap-target" class="card card-tabs">
           <div class="card-content">
           	<div class="row">
              <div class="col s12" style="color:red;">
                 Generate all pdf 
                </div>
            </div>
            <div class="row">
              <div class="col s12 center">
			    <a href="{{ route('exam_center_nominal_roll_pdf',[10,1,0]) }}" class="btn mt-2"> exam center nominal roll  10 stream-1 Generate</a>
                &nbsp;
                <a href="{{ route('exam_center_nominal_roll_pdf',[12,1,0]) }}" class="btn mt-2">exam center nominal roll 12 stream-1 Generate</a>
				</div>
				 <div class="col s12 center">
			    <a href="{{ route('exam_center_nominal_roll_pdf',[10,2,0]) }}" class="btn mt-2"> exam center nominal roll  10 stream-2 Generate</a>
                &nbsp;
                <a href="{{ route('exam_center_nominal_roll_pdf',[12,2,0]) }}" class="btn mt-2"> exam center nominal roll  12 stream-2 Generate</a>
				</div>
              </div>
            </div>
        </div>
        @endif
      </div>
	</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/singleexamcenter_details.js') !!}"></script> 
@endsection 

