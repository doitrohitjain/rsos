@extends('layouts.default')
@section('content')
<div id="main">
	<div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
        <div class="container">
            <div class="row">
				<div class="col s12 m6 l6">
					<h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
				</div>
				<div class="col s12 m6 l6 right-align-md">
					<ol class="breadcrumbs mb-0">
						<li class="breadcrumb-item"><a href="index-2.html">Home</a></li>
						<li class="breadcrumb-item"><a href="#">Form</a></li>
						<li class="breadcrumb-item active">Form Layouts</li>
					</ol>
				</div>
            </div>
        </div>
    </div>
	<div class=" s12">
        <div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<p class="caption mb-0">
							<h6>AI Center Update Form 
								<span style="margin-left: 85%;">
									<a href="{{ route('aicenterusers.index') }}" class="btn btn-xs btn-info pull-right">Back</a>
								</span>
							</h6>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col s12">
						<div id="html-validations" class="card card-tabs">
							<div class="card-content">
								<div id="html-view-validations">
									{!! Form::model($user, ['method' => 'PATCH','route' => ['aicenterusers.update', $user->id],'id' => $model]) !!}
										<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
										<div class="row">
											<div class="col m4 s12">
												@php $lbl="एआई केंद्र  नाम (AI Center Name)"; $fld='college_name'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2,]); !!}
													<input type="hidden" name="old_user_id" value ="{{$user->user_id}}">
													@include('elements.field_error')
													<input type="hidden" name='type' value='2'>					
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="एआई केंद्र कोड (AI Center Code)"; $fld='ai_code'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl='SSOID(SSOID)'; $placeholder = "Select ". $lbl; $fld='ssoid'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!! Form::select($fld,@$allssoid,$user->$fld,['class' => 'select2 browser-default form-control ','placeholder' => $placeholder,]) !!}
												@include('elements.field_error')
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m4 s12">
												@php $lbl='बैंक का नाम (Bank Name)'; $placeholder = "Select ". $lbl; $fld='school_account_bank_name'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!! Form::select($fld,@$banks,$user->$fld,['class' => 'select2 browser-default form-control banks','placeholder' => $placeholder,]) !!}
													@include('elements.field_error')
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="एआई केंद्र खाता संख्या(AI Center Account Number)"; $fld='school_account_number'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>25,'minLength'=>12]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="एआई केंद्र खाते का IFSC कोड(AI Center Account's IFSC Code)"; $fld='school_account_ifsc'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">		{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>11,'minLength'=>11]); !!}
												@include('elements.field_error')	
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m4 s12">
												@php $lbl="प्रिंसिपल नाम(Principal Name)"; $fld='principal_name'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">										{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="प्रिंसिपल मोबाइल नंबर(Principal Mobile Number)"; $fld='principal_mobile_number'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>10,'minLength'=>10]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="नोडल अधिकारी का नाम(Nodal Officer Name)"; $fld='nodal_officer_name'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="नोडल अधिकारी मोबाइल नंबर(Nodal Officer Mobile Number)"; $fld='nodal_officer_mobile_number'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>10,'minLength'=>10]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="पिन कोड(Pin Code)"; $fld='pincode'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>6,'minLength'=>6]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="ईमेल(Email)"; $fld='email'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">{!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
													@include('elements.field_error')	
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m6 s12">
												@php $lbl='ज़िला (District)'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!! Form::select($fld,@$district_list,$user->$fld,['class' => 'select2 browser-default form-control district_id','placeholder' => $placeholder,]) !!}
												@include('elements.field_error')
												</div>
											</div>
											<div class="col m6 s12">
												@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!! Form::select($fld,@$block_list,$user->$fld,['class' => 'select2 browser-default form-control block_id','placeholder' => $placeholder,'id'=>'block']) !!}
													@include('elements.field_error')
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m10 s12 mb-3">
												   <a href="{{ route('aicenterusers.create') }}" class="btn cyan waves-effect waves-light right"> <i class="material-icons right">clear</i>Reset</a>
											</div>
											<div class="col m2 s12 mb-3">
											  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
												<i class="material-icons right">send</i>
											  </button>
											</div>
										</div>
									{!! Form::close() !!}
								</div>
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
<script src="{!! asset('public/app-assets/js/bladejs/aicenter_details.js') !!}"></script> 
@endsection





