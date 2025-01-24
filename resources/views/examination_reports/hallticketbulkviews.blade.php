@extends('layouts.default')
@section('content')
<link href="https://fonts.googleapis.com/css2?family=Hind&display=swap" rel="stylesheet">
<style>
body {
	  font-family: 'Hind', serif;
	}
</style>
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
			 @if($user_role == 59 || $user_role == 72)
				 <div id="tap-target" class="card card-tabs">
					<h4 class="header">&nbsp;<span style="color:green;">Download Enrollment Wise Hall Ticket </span></h4>
					 <h6 style="color:red;">Note: प्रवेश पत्र(Admit Card) को संदर्भ केन्द्र से प्रमाणित करने की आवश्यकता नहीं है।  </h6>

					<div class="card-content">
						{!! Form::open(array('route' => 'hall_ticket_single_enrollment_download','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						<div class="col m12 s12">
							@php $lbl='नामांकन संख्या (Enrollment)'; $placeholder = "Select ". $lbl; $fld='enrollment'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::text($fld,null,['class' => 'form-control num','placeholder' => $placeholder]) !!}
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
						<a href="{{route('hallticketbulkviews')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 
				 @endif
			@if($user_role == 59 || $user_role == 72)
				 <div id="tap-target" class="card card-tabs">
					<h4 class="header">&nbsp;<span style="color:blue;">Generate Enrollment Wise Hall Ticket </span></h4>

					<div class="card-content">
						{!! Form::open(array('route' => 'hallticketsinglegeneratesuperadminenrollment','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						<div class="col m12 s12">
							@php $lbl='नामांकन संख्या (Enrollment)'; $placeholder = "Select ". $lbl; $fld='enrollment'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::text($fld,null,['class' => 'form-control num','placeholder' => $placeholder]) !!}
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
						<a href="{{route('hallticketbulkviews')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 
				 @endif
			@if($user_role == $developeradminrole || $user_role == $examination_department ||  $user_role == 58)

				<div id="tap-target" class="card card-tabs">
					<h4 class="header">&nbsp;<span style="color:green;">Download Generated AI Center Hall Ticket </span></h4>

					<div class="card-content">
						{!! Form::open(array('route' => 'hallticketbulkviews','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						@if($user_role == $developeradminrole || $user_role == $superadminrole || $user_role == $examination_department)
							<div class="col m3 s12">
							@php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						@elseif($user_role == $aicenterrole)
							@php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
						{!!Form::hidden($fld,@$ai_codes,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
						@endif

						<div class="col m3 s12">
							@php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'required'=>'required']) !!}
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
					</div>
					</div><br>
					<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn green waves-effect waves-light right  " type="submit" name="action"> Download
						</button>
					</div>
					<div class="col m2 s12 mb-2">
						<a href="{{route('hallticketbulkviews')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 
				
			@else
			  			@if($user_role == $developeradminrole || $user_role == $examination_department ||  $user_role == 58)
					<div id="tap-target" class="card card-tabs">
						<h4 class="header">&nbsp;Download Generated AI Center Hall Ticket </h4>
					<div class="card-content">

						<div class="row">
						</div>
						<div class="row">
							<div class="col s12 center">
								@php $msg= "Not Found"; @endphp
								@if($stream == 2) 
									@if(file_exists($path) || file_exists($path2) )
										@if(file_exists($path))
											<a href="{{ route('getDownloadhallticketaicodecenterwise',[10,2]) }}" class="btn mt-2"> AI Center Hall Ticket 10 Stream-2 Download</a>
											&nbsp;  
										@endif
										@if(file_exists($path2))
											<a href="{{ route('getDownloadhallticketaicodecenterwise',[12,2]) }}" class="btn mt-2"> AI Center Hall Ticket 12 Stream-2 Download</a>
											&nbsp; 
										@endif 
									@else
										<span style="color:red;font-size:24px;"> {{ $msg }}</span>
									@endif  
								@elseif($stream == 1) 
									@if(file_exists($path) || file_exists($path2) )
										@if(file_exists($path))
											<a href="{{ route('getDownloadhallticketaicodecenterwise',[10,1]) }}" class="btn mt-2"> AI Center Hall Ticket 10 Stream-1 Download</a>
											&nbsp;
										@endif
										@if(file_exists($path2)) 
											<a href="{{ route('getDownloadhallticketaicodecenterwise',[12,1]) }}" class="btn mt-2"> AI Center Hall Ticket 12 Stream-1 Download</a> 
										@endif
									@else
										<span style="color:red;font-size:24px;"> {{ $msg }}</span>
									@endif
								@endif
							</div>

							</div>
						</div>
					</div>
			@endif 
			@endif 
			@if($user_role == $developeradminrole || $user_role == $examination_department  || $user_role == 58)
				<div id="tap-target" class="card card-tabs">
					<h4 class="header" style="color:blue;">&nbsp;Generate AI Center Hall Ticket </h4>

					<div class="card-content">
						{!! Form::open(array('route' => 'downloadhallticketbulviewpdf','method'=>'POST')) !!}
						<div class="row">
						<div class="row">
						@if($user_role == $developeradminrole || $user_role == $superadminrole || $user_role == $examination_department)
							<div class="col m3 s12">
							@php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'ai_code','required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						@elseif($user_role == $aicenterrole)
							@php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
						{!!Form::hidden($fld,@$ai_codes,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
						@endif

						<div class="col m3 s12">
							@php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'course','required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						<div class="col m3 s12">
							@php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream','required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
					</div>
					</div><br>
					<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn warning waves-effect waves-light right  " type="submit" name="action"> Submit
						</button>
					</div>
					<div class="col m2 s12 mb-2">
						<a href="{{route('hallticketbulkviews')}}" class="btn cyan waves-effect waves-light right">Reset </a>
					</div>
					</div> 
					{{ Form::close() }}
					</div>
				</div> 
				@if($user_role == $developeradminrole )
					@php $current_material_genertion_stream = Config::get("global.current_material_genertion_stream"); @endphp 
					<div id="tap-target" class="card card-tabs">
						<h4 class="header">&nbsp;Generate All AI Center Hall Ticket Bulk (Current Stream {{ $current_material_genertion_stream }}) </h4>
					<div class="card-content"> 
						<div class="row">
							<div class="col s12 center">
								<a href="{{ route('hall_ticket_bulk_downloads_all',[10,$current_material_genertion_stream,0]) }}" class="btn mt-2"> Generate AI Centre Hall Ticket 10 stream-{{ $current_material_genertion_stream }} </a>
								&nbsp;
								<a href="{{ route('hall_ticket_bulk_downloads_all',[12,$current_material_genertion_stream,0]) }}" class="btn mt-2">  Generate AI Centre Hall Ticket 12 stream-{{ $current_material_genertion_stream }}</a>
							</div> 
						</div>
						</div>
					</div>
				@endif
			@endif
		</div>
	</div>
</div>
@endsection
