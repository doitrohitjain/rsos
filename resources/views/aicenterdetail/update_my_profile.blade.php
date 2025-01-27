@extends('layouts.default')
@section('content')
<!-- BEGIN: Page Main-->
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
		</div>
		
		<div class="row">
			<div class="">
				<div class="seaction col l12 s12 m12">
					<div class="card">
						<div class="card-content">
							<p class="caption mb-0">
								<h6>
									<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="RSOS"/>
									<span style="color:blueviolet;">कृपया नवीनतम सरकारी दिशानिर्देशों के अनुसार जिला और ब्लॉक को अपडेट करें।
										(Please update the district & block according to the most recent government guidelines.)</span>
								</h6>
								<h6>
									<span style="font-weight: 800;">
										<table class="table">
											<tr>
											@if(@$role_id == @$aicenterrole)
												<th colspan="3">
													@php $lbl = "AI Center Name (Code)";$fld = "college_name"; @endphp
													<td>{{ @$user->ai_code }} - {{ @$user->$fld }} ( @php $fld = "ssoid"; @endphp {{ @$user->$fld }} )</td>
												</th>
											@endif	
												<th>
													@php $lbl = "District";$fld = "district_id"; @endphp
													<td>{{ $lbl }} : {{ @$district_list[$user->$fld] }}</td>
												</th>
												<th>
													@php $lbl = "Block";$fld = "block_id"; @endphp
													<td>{{ $lbl }} :{{ @$block_list[$user->$fld] }}</td>
												</th> 
												<th>
													@php $lbl = "New District";$fld = "temp_district_id"; @endphp
													<td>{{ $lbl }} : {{ @$district_list[$user->$fld] }}</td>
												</th>
												<th>
													@php $lbl = "New Block";$fld = "temp_block_id"; @endphp
													<td>{{ $lbl }} :{{ @$block_list[$user->$fld] }}</td>
												</th> 
												<th>
													@php $lbl = "Pincode";$fld = "pincode"; @endphp
													<td>{{ $lbl }} :{{ @$user->$fld }}</td>
												</th>
												
											</tr>

										</table>
								</h6>
							</p>
						</div>
					</div>


					<div class="row">
					<div class="col s12">
					<div id="html-validations" class="card card-tabs">
						<div class="card-content">
						<div id="html-view-validations">
							
						{!! Form::model($user, ['route' => ['update_my_profile'],'id' => $model]) !!}
							
							<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
							<!-- <div class="row hide">
								<div class="col m4 s12">
									@php $lbl="एआई केंद्र  नाम (AI Center Name)"; $fld='college_name'; @endphp 
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2,]); !!}
									<input type="hidden" name="old_user_id" value ="{{@$user->user_id}}">
										@include('elements.field_error')
									<input type="hidden" name='type' value='2'>					
									</div>
								</div>
								<div class="col m4 s12">
									@php $lbl="एआई केंद्र कोड (AI Center Code)"; $fld='ai_code'; @endphp 
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
										@include('elements.field_error')	
									</div>
								</div>
								<div class="col m4 s12">
									@php $lbl='SSOID(SSOID)'; $placeholder = "Select ". $lbl; $fld='ssoid'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
										{!! Form::select($fld,@$allssoid,@$user->$fld,['class' => 'select2 browser-default form-control ','placeholder' => $placeholder,]) !!}
										@include('elements.field_error')
									</div>
								</div>
							</div> -->



							<div class="row">
							
							
							<div class="col m4 s12">
								@php $placeholder = $lbl="एआई केंद्र का बैंक का नाम(AI Center Bank's Name)"; $fld='school_account_bank_name'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
									{!! Form::select($fld,@$banks,@$user->$fld,['class' => 'select2 browser-default form-control ','placeholder' => $placeholder,]) !!}
									@include('elements.field_error')
								</div>
							</div>
							
							<div class="col m4 s12">
								@php $lbl="एआई केंद्र खाता संख्या(AI Center Account Number)"; $fld='school_account_number'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>25,'minLength'=>12]); !!}
									@include('elements.field_error')	
								</div>
							</div>
							<div class="col m4 s12">
								@php $lbl="एआई केंद्र खाते का IFSC कोड(AI Center Account's IFSC Code)"; $fld='school_account_ifsc'; @endphp 
								<span class="small_lable" style="font-size:10px;">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
										{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>11,'minLength'=>11]); !!}
								@include('elements.field_error')	
								</div>
							</div>

							


							

						</div>
								<div class="row">
									

							<div class="col m4 s12">
								@php $lbl="प्रिंसिपल नाम(Principal Name)"; $fld='principal_name'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
									@include('elements.field_error')	
								</div>
							</div>
							<div class="col m4 s12">
								@php $lbl="प्रिंसिपल मोबाइल नंबर(Principal Mobile Number)"; $fld='principal_mobile_number'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>10,'minLength'=>10]); !!}
									@include('elements.field_error')	
								</div>
							</div>

							<div class="col m4 s12">
								@php $lbl="ईमेल(Email)"; $fld='email'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
									@include('elements.field_error')	
								</div>
							</div>

							
							<div class="col m4 s12">
								@php $lbl="नोडल अधिकारी का नाम(Nodal Officer Name)"; $fld='nodal_officer_name'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
									@include('elements.field_error')	
								</div>
							</div>
								
							 
							
							
							<div class="col m4 s12">
								@php $lbl="नोडल अधिकारी मोबाइल नंबर(Nodal Officer Mobile Number)"; $fld='nodal_officer_mobile_number'; @endphp 
								<span class="small_lable" style="font-size:10px;">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>10,'minLength'=>10]); !!}
									@include('elements.field_error')	
								</div>
							</div>
							<div class="col m4 s12">
								@php $lbl="पिन कोड(Pin Code)"; $fld='pincode'; @endphp 
								<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>6,'minLength'=>6]); !!}
									@include('elements.field_error')	
								</div>
							</div>
							</div>
							<div class="row hide5">
								<div class="col m6 s12">
									@php $lbl='ज़िला (District)'; $placeholder = "Select ". $lbl; $fld='temp_district_id'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$district_list,@$user->$fld,['class' => 'select2 browser-default form-control district_id','placeholder' => $placeholder,]) !!}
									@include('elements.field_error')
								</div>
							</div>
							<div class="col m6 s12">
							@php $lbl='चयन  ब्लॉक (Block)'; $placeholder = "Select ". $lbl; $fld='temp_block_id'; @endphp
							<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
								<div class="input-field">
								{!! Form::select($fld,@$block_list,@$user->$fld,['class' => 'select2 browser-default form-control block_id','placeholder' => $placeholder,'id'=>'block']) !!}
								@include('elements.field_error')
						
								</div>
							</div>
								</div> 
								
							 <div class="row">
							<div class="col m10 s12 mb-3">
									<a href="{{ route('update_my_profile') }}" class="btn cyan waves-effect waves-light right"> <i class="material-icons right">clear</i>Reset</a>
									</div>
								<div class="col m2 s12 mb-3">
								<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
									<i class="material-icons right">send</i>
								</button>
								</div>
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
<script src="{!! asset('public/app-assets/js/bladejs/update_my_profile_aicenter_details.js') !!}"></script> 
@endsection





