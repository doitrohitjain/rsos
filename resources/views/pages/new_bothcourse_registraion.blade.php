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

				<form method="post" action="{{ route('new_term_conditions') }}">
					<div class="row">
					@if(!empty($egetssoid))
					<input type="hidden" name="ssoid" value="{{$egetssoid}}"/>
					@endif
					<div class="row">
						<div class="input-field col s12">
							<center>
								
								@if($course == 10)
								<button class="btn cyan waves-effect waves-light " type="submit" name="action"> 
									Apply For 10th Course
								</button>  
								@endif

								@if($course == 12) 
									<button class="btn cyan waves-effect waves-light " type="submit" name="action"> 
										Apply For 12th Course
									</button> 
								@endif

								@if($course == 14)
									<button class="btn cyan waves-effect waves-light " type="submit" name="action"> 
										Apply For 10th Course
									</button> 
										&nbsp;
										<button class="btn cyan waves-effect waves-light " type="submit" name="action"> 
											Apply For 12th Course
										</button> 
								@endif
							 </center>
						</div>
					</div>
					</div>
				</form>
				
					
				</div>
				 
              </div>
            </div>
        </div>
      </div>
	</div>
</div>
@endsection
