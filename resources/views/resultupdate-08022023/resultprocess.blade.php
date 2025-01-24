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
       @if(isset($arr) && !empty($arr))
		<div id="tap-target" class="card card-tabs">
           <div class="card-content">
           	<div class="row">
               <div class="col s12 m6 l6">
					<!-- <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5> -->
				</div>
            </div>
            <div class="row">
                <ol style='font-size:30px;'>
                    @foreach($arr as $arr1)
                        <div class="col s12">
                            <li>
                                <a href="{{$base_url.$arr1['link']}}" class="" target='_blank' style='font-size:30px;'><b>{{$arr1['label']}}</b></a>  
                            </li>              
                        </div>
                    @endforeach 
                </ol>
            </div>
        </div>
        @endif
      </div>
	</div>
</div>
@endsection
