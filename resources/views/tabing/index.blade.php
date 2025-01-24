@extends('layouts.default')
@section('content')


<!-- Tab content -->
										 
<style>
/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

/* for fade in tabs */
.tabcontent {
  animation: fadeEffect 1s; /* Fading effect takes 1 second */
}

/* Go from zero to full opacity */
@keyframes fadeEffect {
  from {opacity: 0;}
  to {opacity: 1;}
}
</style>

<div id="main">
	<div class="row">
		@include('elements.breadcrumbs') 
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row right"> 
										<!--<a href="{{route('create')}}" class="btn btn-xs btn-info pull-right">ADD School</a>-->
									</div>
									
									<div class="row"> 
										<!-- Tab links -->
										<div class="tab">
											@foreach(@$tab_arr as $data)
												@if($data['status'])
													<button class="{{ @$data['class'] }}" data-tabid="{{ @$data['id'] }}">{{ @$data['name'] }}</button>
												@endif
											@endforeach
										</div>
										@foreach(@$tab_arr as $data) 
								
											@if($data['status']) 
												<div id="{{ @$data['id'] }}" class="tabcontent">
													<h3>{{ @$data['name'] }}</h3>
													<ul> 
													@if(isset($content[@$data['id']])) 
														@foreach(@$content[@$data['id']] as $content_data)  
															@php if(@$content_data['status']){ @endphp 
																<li>@php echo "<b>".@$content_data['linktext'].'</b> : '.@$content_data['link'] @endphp</li>
															@php } @endphp
														@endforeach
													@else
														<li>Data Not Found</li>
													@endif
													</ul>
												</div>
											@endif
										@endforeach
										
										
									</div>
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

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/tabing.js') !!}"></script>
@endsection 
