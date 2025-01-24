<div class="row">
	<div class="col m12 s12 "> 
		<div class="col m4 s8 mb-1">
		</div>
		<div class="col m1 s8 mb-1">
		</div>
		@if(@$role_id == Config::get("global.super_admin_id"))
			<div class="col m1 s8 mb-1">
				<p>
					<label for="tnc_select1" style="font-size: 14px;font-weight: bolder;color: black !important;">
						 Verifier Rejection
					</label>
				</p>
			</div>
		@endif
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
				</p></div>
			@endforeach
		</div>
		<div class="col m2 s8 mb-1 {{ @$remarksLbl }}">
			<p ><label for="tnc_select1" style="font-size: 14px;font-weight: bolder;color: black !important;">
				Reason(If Any Rejected)
			</label>
			</p>
		</div>
	</div> 
	@php $counter = 0; @endphp
	@foreach($documentInput as $fld => $lbl) 
		@php $counter++; @endphp		
		@if(@$role_id == config("global.super_admin_id"))
			@php 
				$tempFld = str_replace("dept_","",$fld);
				$verifierStatus = "verifier_" . $tempFld ;
				$verifierStatusRemarks = "verifier_" . $tempFld . "_remarks";
				
			@endphp
		@endif 
		<div class="col m12 s12 "> 
			<div class="col m4 s8 mb-1">
				<div class="file-path-wrapper"> 
					<span style="font-size:20px;font-weight: bold;color:#000;border-bottom:1px dotted rgba(0, 0, 0, 0.42)">
					<b>@php echo @$lbl['label'] . Config::get('global.starMark'); @endphp</b></span> 
				</div>
			</div>  
			<div class="input-field col m1 s10 mb-3">
				
				@if(@$rejectionType && $rejectionType == 'clarification')
					@php
						$fldTemp = $lbl["main_fld_name"];
						$path = public_path($studentDocumentPath . "/" . @$master->$fldTemp);
					@endphp 
					@if(!empty(@$master->$fldTemp))
						@php  
							$filePath = url(('public/'.$studentDocumentPath . @$master->$fldTemp));
						@endphp	
						@if(file_exists($path))	
							<a href="{{ $filePath }} " target="_blank" class="invoice-action-view" title="Click here to View clarification document"><i class="material-icons">remove_red_eye</i>
							</a>
						@else
							{{ "Not Found" }}
						@endif				
					@endif
				@else
					@php
						$fldTemp = $lbl["main_fld_name"];
						$path = public_path($studentDocumentPath . "/" . @$master->$fldTemp); 
					@endphp
					@if(!empty(@$master->$fldTemp))
						@php  
							$filePath = url(('public/'.$studentDocumentPath . "/" . @$master->$fldTemp)); 
						@endphp	
						@if(file_exists($path))	
							<a href="{{ $filePath }} " target="_blank" class="invoice-action-view" title="Click here to View."><i class="material-icons">remove_red_eye</i>
							</a>
						@else
							{{ "Not Found" }} 
						@endif				
					@endif
				@endif 
			</div> 
			
			@if(@$role_id == Config::get("global.super_admin_id"))
				<div class="input-field2 col m1 s10 mb-3"> 
					@if(@$verifierStatus && @$documentVerifierVerifications->$verifierStatus == 2 && @$documentVerifierVerifications->$verifierStatusRemarks)
						@php
							$modelContent = '<center><h4> ' . @$documentVerifierVerifications->$verifierStatusRemarks . ' </h4></center>';
						@endphp
						<span class="waves-effect waves-light  modal-trigger modalCls" style="color:blue;float:right;" data-content="{{ $modelContent }}">
							<sup>Verifier Reason</sup>
						</span>
					@else
						<sup><span class="chip lighten-5 green green-text">Approved</span></sup>
					@endif
				</div>  
			@endif
			<div class="input-field2 col m4 s10 mb-3">
				<input type="hidden" name="mainitem[{{ @$lbl['main_fld_name'] }}]" value="{{  @$lbl['main_fld_name'] }}">
				@foreach (@$fresh_student_doc_update_status as $k => $v)
					<div class="col m6">
						<center>
							<p>
								@php $baseClsName = "filled-in-"; @endphp 
								@php $color = "green"; @endphp
								@if($k == 2)
									@php $color = "red"; @endphp
								@endif
								@php $baseClsName = $baseClsName . $color; @endphp 
								<label>
									<input type="checkbox" name="{{ @$fld }}[{{ $k }}]" class="filled-in  center-align cls_fresh_student_doc_update_status course cls_{{ $fld }} {{ @$baseClsName }} background-color: #00c853 !important;" data-item_name="{{ $v }}" data-main_item_name="{{ $fld }}" id="{{ $v  . '_' .$fld }}" />
									<span></span>
								</label>
							</p>
							</center>
						<div class="input-field">
						</div>
					</div> 
				@endforeach
				@include('elements.field_error')
			</div>  

			@php $fld = $fld . "_remarks"; $documentVerificationDetailsVal = null; @endphp
			@php $showStatus = "hide"; @endphp
		
			<div class="input-field2 col m2 s10 mb-3 cls_{{ $fld }}_div {{ @$showStatus }}"> 
				@if(@$documentVerificationDetails[@$fld] && @$documentVerificationDetails[@$fld] == 2)
					@php $documentVerificationDetailsVal = @$documentVerificationDetails[@$fld]; @endphp
				@endif
				{!! Form::textarea($fld,$documentVerificationDetailsVal, array('class'=>'form-control cls_' . $fld . " ", 
				'rows' => 2, 'cols' => 2,'maxlength' => "3000",'style' => 'height:2rem;','placeholder'=>'Rejection Reason')) !!}
			</div>  
			
		</div>   
	@endforeach


	</div>


<style>
	[type="checkbox"].filled-in-green:checked + span:not(.lever):after {
		z-index: 0;
		top: 0;
		width: 20px;
		height: 20px;
		border: 2px solid #4caf50!important;
		background-color: #4caf50!important;
	}

	[type="checkbox"].filled-in-red:checked + span:not(.lever):after {
		z-index: 0;
		top: 0;
		width: 20px;
		height: 20px;
		border: 2px solid #e91818 !important;
		background-color: #e91818 !important;
	}


</style>