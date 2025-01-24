@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="col s12 m12 l12">
						<div class="card">  
						</div>
					</div>
					<div class="col s12 m12 l12"> 
						<div id="Form-advance" class="card card card-default scrollspy">
							<div class="card-content">
								<h4 class="card-title">{{ $page_title; }} </h4>  
								
								<div class="col m12 s12 mb-1">
									<marquee>										
										<span class="" style="float:left !important;color:red;"> 
											Note : Please click the "Submit Clarification" button to complete the submission of your clarification.
										</span>
									</marquee>
									<span class="" style="float:left !important;color:blue;"> 
										Note: The photograph and signature must be between 10 KB and 50 KB in size, and in JPEG, PNG, or GIF format. Other files must be between 50 KB and 500 KB in size, and in JPEG, PNG, or GIF format and Use the  <i class="material-icons mr-2" title="Clarification Reason"> info_outline </i>  icon for details of rejection reasons.</span>
								</div>
								
								@include('elements.rejected_document_input') 
							</div> 

							<div class="row"> 
								<div class="col s12 m12 l12"> 
									<div class="step-actions right">
										<div class="">
											<div class="col m8 s12 mb-1">
											   {{ Form::open(['route' => [request()->route()->getAction()['as'],$estudent_id],"id" => $model]) }}
												<input type="hidden"  name="type" value="{{ Crypt::encrypt(1) }}">
												<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
												<button class="waves-effect waves-teal gradient-90deg-deep-orange-orange white-text secondary-content submitBtnCls submitconfirms btn show_confirm green" type="submit" name="action">
												Submit Clarification
												</button>
												{{ Form::close() }}
										</div>
										<div class="col m3 s12 mb-3">
										 <a href="{{route('studentsdashboards')}}" class="waves-effect waves dark btn btn-primary btn_disabled next-step">Back</a> 
										</div> 
										</div>
									</div>
								</div>
							</div>
							<br>
						</div>
					</div>
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/for_rejected_document_details.js') !!}"></script> 
@endsection
