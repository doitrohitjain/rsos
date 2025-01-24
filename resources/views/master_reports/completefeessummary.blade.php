@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
	  <div id="breadcrumbs-wrapper" data-image="./public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
	          <div class="card">
					<div class="card-content">
					 <h6><a href="{{route('completeFeesSummarydownload')}}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text right mb-2 mr-1">Complete Fees Summary Download Format</a></h6>
					<h6>Complete Fees Summary<h6>
					</div>
					</div>
			
      <div id="tap-target" class="card card-tabs">
        <div class="card-content">
             {!! Form::open(array('route' => 'downloadcompleteFeesSummaryexcel','method'=>'POST')) !!}
            <div class="row">
            <div class="col m4 s12">
                @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp</span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align course','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
                </div>
                </div>

                <div class="col m4 s12">
                @php $lbl='Stream'; $placeholder = "Select ". $lbl ; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp</span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div>
            </div>
			 <div class="col m4 s12">
                @php $lbl='Gender'; $placeholder = "Select ". $lbl; $fld='gender_id'; @endphp
                <span class="small_lable">@php echo $lbl  @endphp </span>
                    <div class="input-field">
                    {!! Form::select($fld,@$gender_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                    @include('elements.field_error')
                </div>
            </div>
			 </div>
			<div class="row">
			 <div class="col m4 s12">
                @php $lbl='AI Center'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                <span class="small_lable">@php echo $lbl  @endphp </span>
                    <div class="input-field">
                    {!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                    @include('elements.field_error')
                </div>
            </div>
			<div class="col m4 s12">
                @php $lbl='Are You From Rajasthan'; $placeholder = "Select ". $lbl; $fld='are_you_from_rajasthan'; @endphp
                <span class="small_lable">@php echo $lbl  @endphp </span>
                <div class="input-field">
                    {!! Form::select($fld,@$are_you_from_rajasthan,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                    @include('elements.field_error')
                </div>
            </div>
			<!-- <div class="col m4 s12">
                @php $lbl='FeeTypes'; $placeholder = "Select ". $lbl; $fld='feeType'; @endphp
                <span class="small_lable">@php echo $lbl  @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$feeType,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div> -->
        </div>
          
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Submit
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="{{route('completeFeesSummary')}}" class="btn cyan waves-effect waves-light right">Reset </a>
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

