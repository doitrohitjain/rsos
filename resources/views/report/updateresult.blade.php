@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
        <div class="col s12">
            <div class="container"> 
                <div class="section section-data-tables"> 
                    <div class="row">
                        <div class="col s12">
                            <div class="card">
                                <div class="card-content">
                                    @include('elements.filters.filter')
								</div>
							</div>
						</div>
					</div>
				</div>
          <div class="content-overlay"></div>
        </div>
		</div>
    </div>
</div> 
@endsection