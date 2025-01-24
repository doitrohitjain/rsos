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
             {!! Form::open(array('route' => 'aicenterWiseCountFreshAndSupplementary','method'=>'POST')) !!}
            <div class="row">
                <div class="col m3 s12">
                @php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream2','required']) !!}
                @include('elements.field_error')
            </div>
            </div>
            <div class="col m3 s12">
                  @php $lbl='Fresh Supplementary Type'; $placeholder = "Select ". $lbl; $fld='fresh_supp_option'; @endphp
                  <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                  <div class="input-field">
                      {!! Form::select($fld,@$fresh_supp_options,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream','required']) !!}
                      @include('elements.field_error')
                  </div>
                </div>
                
          
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Submit
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
  
      </div>
	</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/singleexamcenter_details.js') !!}"></script> 
@endsection 