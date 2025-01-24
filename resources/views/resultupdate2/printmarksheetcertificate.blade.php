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
              <div class="col s12 center">
			    <a href="{{ route('downloadduplicatemarksheet',[1,$enrollment]) }}" class="btn mt-2">Download Duplicate MarkSheet</a>
                &nbsp;
                <a href="{{ route('downloadduplicatecertificate',[1,$enrollment]) }}" class="btn mt-2">Download Duplicate Certificate</a>
				</div>
				 <div class="col s12 center">
			    <a href="{{ route('downloadduplicatemarksheet',[2,$enrollment]) }}" class="btn mt-2">Download Revised Marksheet</a>
                &nbsp;
                <a href="{{ route('downloadduplicatecertificate',[2,$enrollment]) }}" class="btn mt-2">Download Revised Certificate</a>
				</div>
              </div>
            </div>
        </div>
      </div>
	</div>
</div>
@endsection
