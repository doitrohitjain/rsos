<div class="row">
	<div class="col m12 s12 "> 
		<div class="col m4 s8 mb-1">
			
		</div>
		<div class="col m1 s8 mb-1">
		</div>
		<div class="col m4 s8 mb-1"> 
			@foreach (@$fresh_student_doc_update_status as $k => $v)
				<div class="col m6" >
					<p>
						@php $color = "green"; @endphp
						@if($k == 2)
							@php $color = "red"; @endphp
						@endif
						<label for="tnc_select1" style="font-size: 18px;font-weight: bolder;color: {{ $color }} !important;">
							{{ $v }}
						</label>
					</p>
				</div>
			@endforeach
		</div>
		<div class="col m3 s8 mb-1 {{ @$remarksLbl }}">
			<p ><label for="tnc_select1" style="font-size: 14px;font-weight: bolder;color: black !important;">
				Reason(If Any Rejected)
			</label>
			</p>
		</div>  
	</div>
	 
	<div class="col m12 s12 "> 
		<div class="col m2 s8 mb-1">
			
		</div>
		<div class="col m3 s8 mb-1" style="font-size:14px;color: #03a9f4;font-family: 'Muli';">
			Verification Status
		</div>
		<div class="col m4 s8 mb-1"> 
			<div class="">
				<input type="hidden" name="mainitem[{{ @$fld }}]" value="{{  @$fld }}">
				@foreach (@$fresh_student_doc_update_status as $k => $v)
					@php 
						$role_id = Session::get('role_id');
						$preFix = "verifier_";
					@endphp
					@if(@$role_id == config("global.super_admin_id"))
						@php $preFix = "dept_"; @endphp
					@endif 
					@php 
						$extraFld = $preFix . @$fld . "_is_verify"; 
						$extraFldRemarks = $preFix . @$fld . "_is_verify_remarks"; 
					@endphp
					<div class="col m6 ">
						<center>
							<p>
								@php $baseClsName = "filled-in-"; @endphp 
								@php $color = "green"; @endphp
								@if($k == 2)
									@php $color = "red"; @endphp
								@endif
								@php $baseClsName = $baseClsName . $color; @endphp 
								<label> 
									<input type="checkbox" name="{{ @$extraFld }}[{{ $k }}]" class="filled-in  center-align cls_fresh_student_doc_update_status course cls_{{ @$fld }} {{ @$baseClsName }} background-color: #00c853 !important;" data-item_name="{{ $v }}" data-kConter="{{ $kCounter }}" data-main_item_name="{{ @$fld }}" id="{{ @$v  . '_' .@$fld }}" />
									<span></span>
								</label>
							</p>
						</center>
					</div> 
				@endforeach
				@include('elements.field_error')
			</div> 
		</div>
		<div class="col m3 s8 mb-1"> 
			@php $fld = @$fld . "_remarks"; $documentVerificationDetailsVal = null; 
				
			@endphp
			@php $showStatus = "hide"; @endphp
			<div class="input-field2 col m12 s12 mb-3 cls_{{ $fld }}_div {{ @$showStatus }}"> 
				@if(@$documentVerificationDetails[@$fld] && @$documentVerificationDetails[@$fld] == 2)
					@php $documentVerificationDetailsVal = @$documentVerificationDetails[@$fld]; @endphp
				@endif
				{!! Form::textarea($extraFldRemarks,$documentVerificationDetailsVal, array('class'=>'form-control cls_' . $fld . " ", 
				'rows' => 2, 'cols' => 2,'maxlength' => "3000",'style' => 'height:2rem;','placeholder'=>'Rejection Reason')) !!}
			</div> 
		</div>
		
	</div>
</div> 
