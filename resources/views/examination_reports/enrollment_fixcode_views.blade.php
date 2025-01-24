@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
		<div class="col s12">
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
					<h4 class="header">&nbsp;<span style="color:green;">Download Fix code </span></h4>

					<div class="card-content">
						{!! Form::open(array('route' => 'getDownloadfixcodesingle','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						<div class="col m3 s12">
							@php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes course','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						<div class="col m3 s12">
							@php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>

						  <div class="col m3 s12">
							@php $lbl='विषयों(Subjects)'; $placeholder = "Select ". $lbl; $fld='subjects'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align examcenter1 subjects','placeholder' => $placeholder]) !!}
							@include('elements.field_error')
							</div>
							</div>

							<div class="col m3 s12">
							@php $lbl='परीक्षा केंद्र(Exam Center)'; $placeholder = "Select ". $lbl; $fld='ecenter'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align  examcenter_details','placeholder' => $placeholder]) !!}
							@include('elements.field_error')
							</div>
							</div>
					</div>
					</div><br>
					<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn green waves-effect waves-light right  " type="submit" name="action"> Download
						</button>
					</div>
					<div class="col m2 s12 mb-2">
						<a href="{{route('enrollment_fixcode_views')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 
				
				<div id="tap-target" class="card card-tabs">
					<h4 class="header">&nbsp;<span style="color:blue;">Generate Fix code </span></h4>

					<div class="card-content">
						{!! Form::open(array('route' => 'enrollment_fixcode_views_requests','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						<div class="col m3 s12">
							@php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes course','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						<div class="col m3 s12">
							@php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>

						  <div class="col m3 s12">
							@php $lbl='विषयों(Subjects)'; $placeholder = "Select ". $lbl; $fld='subjects'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align examcenter1 subject','placeholder' => $placeholder]) !!}
							@include('elements.field_error')
							</div>
							</div>

							<div class="col m3 s12">
							@php $lbl='परीक्षा केंद्र(Exam Center)'; $placeholder = "Select ". $lbl; $fld='ecenter'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align examcenter1 examcenter_detail','placeholder' => $placeholder ,'id'=>'subjectstype1']) !!}
							@include('elements.field_error')
							</div>
							</div>
					</div>
					</div><br>
					<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Generate
						</button>
					</div>
					<div class="col m2 s12 mb-2">
						<a href="{{route('enrollment_fixcode_views')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 


          <div id="tap-target" class="card card-tabs">
           <div class="card-content">
		 <h4 class="header">&nbsp;<span style="color:green;">Bulk Download Stickers</span></h4>
            <div class="row">
              <div class="col s12 center">
            
                <a href="{{ route('getDownloadfixcodezipdownload',[10,1]) }}" class="btn mt-2">Download Student Stickers (Stream1-10th)</a>
                &nbsp;
                  <a href="{{ route('getDownloadfixcodezipdownload',[10,2]) }}" class="btn mt-2" >Download Student Stickers (Stream2-10th)</a>
                &nbsp;
                </div>
                <div class="col s12 center">
                 <a href="{{ route('getDownloadfixcodezipdownload',[12,1]) }}" class="btn mt-2">Download Student Stickers (Stream1-12th)</a>
                &nbsp;
                 <a href="{{ route('getDownloadfixcodezipdownload',[12,2]) }}" class="btn mt-2" >Download Student Stickers(Stream2-12th)</a>
              </div>
            </div>
        </div>
      </div>				
			
		</div>
	</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/singlefixcode_details.js') !!}"></script> 
@endsection 
