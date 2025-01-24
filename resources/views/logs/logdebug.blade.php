@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
	  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
           	<div class="row">
            </div>
            <div class="row">
				<div class="col  center">
					@php
					$path=storage_path('logs\laravel.log');
					$viewfile=asset('storage/laravel.log');
					@endphp
					@if(file_exists($path))
					<a href="{{route('downloadlog')}}" class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange">Download Log</a>
					&nbsp;
					<a href="{{route('deletelog')}}" class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-blue-deep-orange">Delete Log</a>
					</div>
					<a href="{{$viewfile}}" target="_blank" class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-blue-deep-orange">View Log</a>
					</div>
					
					
					@endif
            </div>
        </div>
      </div>
	</div>
</div>
@endsection
