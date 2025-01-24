@extends('layouts.default')
@section('content')
<style>
body {
	  font-family: 'Hind', serif;
	}
</style>
<div id="main">
	<div class="row">
		<div>
			<div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
				<!-- Search for small screen-->
				<div class="container">
					<div class="row">
						<div class="col s12 m6 l6">
							<h5 class="breadcrumbs-title mt-0 mb-0"><span>Aicode wise hall Ticket</span></h5>
						</div>
					</div>
				</div>
			</div>  
				 <div id="tap-target" class="card card-tabs">
					<div class="card-content">
						{!! Form::open(array('route' => 'hall_ticket_single_bulk_aicode_wise_generate','method'=>'POST')) !!}
						<div class="row">
						<div class="col m12 s12">
							@php $lbl='aicode'; $placeholder = "Select ". $lbl; $fld='aicode'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field">
							{!! Form::select($fld,@$finedstudentaicode,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'required'=>'required']) !!}
							@include('elements.field_error')
						</div>
						</div>
						</div>
					</div>
					<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn green waves-effect waves-light right  " type="submit" name="action">  Generate
            
						</button>
					</div>
					</div> 
				{{ Form::close() }}
				</div>
				</div> 
		</div>
	</div>
</div>
@endsection
