@extends('layouts.default')
@section('content')
<style>
	.frees {
		pointer-events: none;
	}
</style>
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item"><a href="">{{ $title }}</a></li>
						</ol>
					</div>
				</div>
			</div>
        </div>
		<div class="col s12">
            <div class="container">
                <div class="seaction">
					<div class="card">
						<div class="card-content">
							<h6>
								<a href="{{route('dgs_listing')}}" class="btn btn-xs btn-info right gradient-45deg-deep-orange-orange">
									back
								</a>
							</h6>
							<h6>&nbsp;</h6>
						</div>
					</div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content"> 
								@include('elements.ajax_validation_block')
									{{ Form::open(['url'=>route('store_profile'),'method'=>'POST','id'=>'TheoryExaminerForm']) }}
									{!! Form::token() !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name='createprofilelink' value='1' id='createprofilelink'>
									<div class="row">
										<div class=" col m5 s13">
											@php $lbl='Select AI Centre'; $fld='ai_code'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            <div class="input-field">
										{!!Form::select(@$fld,@$aicenter_list,old($fld),['class'=>' select2 browser-default  form-control','id'=>'','autocomplete'=>'off','placeholder'=>'Select Ai Center','required']); !!}
										@include('elements.field_error')
										</div>
										</div>
                                        <div class="col m5 s12">
											@php $lbl='Number Of Student'; $fld='studentcount'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                           <div class="input-field">
										    {!!Form::text($fld,old($fld),['type'=>'text','class'=>'examiner_name form-control num','autocomplete'=>'off','maxlength'=>"2",'id'=>'ExaminerName' ,'required','placeholder'=>'Enter Number Of Students']); !!}
                                            @include('elements.field_error')
											</div>
											
										</div>
									</div>		
									<div class="row">
										<div class="col m10 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right gradient-45deg-green-teal" type="submit" name="action">submit 
												<i class="material-icons right"></i>
											</button>
											
										</div>
										<div class="col m2 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">
												<i class="material-icons right">clear</i>Reset
											</button>
										</div>
									</div>
								{{ Form::close() }}
							
                            </div>
                        </div>
                    </div>
                </div> 
			</div>
		</div>
	</div>
</div>				
@endsection
@section('customjs')
   
@endsection 