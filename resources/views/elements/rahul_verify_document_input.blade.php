{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
  'enctype' => 'multipart/form-data', "id" => $model ]) }}
{!! Form::token() !!}
{!! method_field('PUT') !!}
<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
<input type="hidden" name='isAllRejected' value=null id='isAllRejected'>
<div class="row">
	@php $counter = 0; @endphp
	@foreach($documentInput as $fld => $lbl)		
		@php $counter++; @endphp
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
						$path = public_path($studentDocumentPath . "" . @$master->$fldTemp);
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
						$path = public_path($studentDocumentPath . "/" . @$master->$fld);
					@endphp 
					@if(!empty(@$master->$fld))
						@php  
							$filePath = url(('public/'.$studentDocumentPath . "/" . @$master->$fld)); 
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
			<div class="col m2">
                  <label for="tnc_select1">Approve</label>
					<p>
						<label>
							<input type="checkbox" name="checkbox" class="center-align cls_fresh_student_doc_update_status allstreamcourseadmtypes course cls_' . $fld" id="{{$fld}}" />
							<span></span>
						</label>
					</p>
                  <div class="input-field">
                  </div>
                </div>
				
				<div class="col m2">
					<input type="hidden" name="mainitem[{{ @$lbl['main_fld_name'] }}]" value="{{  @$lbl['main_fld_name'] }}">
					<label for="tnc_select1">Rejected</label>
					<p>
					<label>
						<input type="checkbox" name="checkbox" class="center-align cls_fresh_student_doc_update_status allstreamcourseadmtype course cls_' . $fld" id="{{$fld}}" />
						<span></span>
					</label>
					</p>
                  <div class="input-field">
                  </div>
                </div>
				<div class="col m2">
                 <input name="showthis" id= "showthis" class="getvale_<?php echo $fld;?>" size="50" type="text" value="text here" />
                </div>
			@php $fld = $fld . "_remarks"; $documentVerificationDetailsVal = null; @endphp
			@php $showStatus = "hide"; @endphp
			<div class="input-field col m4 s10 mb-3 cls_{{ $fld }}_div {{ @$showStatus }}"> 
				@if(@$documentVerificationDetails[@$fld] && @$documentVerificationDetails[@$fld] == 2)
					@php $documentVerificationDetailsVal = @$documentVerificationDetails[@$fld]; @endphp
				@endif
				{!! Form::textarea($fld,$documentVerificationDetailsVal, array('class'=>'form-control cls_' . $fld . " ", 
			 'rows' => 2, 'cols' => 2, 'style' => 'height:2rem;','placeholder'=>'Rejection Reason')) !!}
			</div> 
		</div>  
	 
	@endforeach

	<div class="col s12 m12 l12"> 
		<div class="step-actions right">
			<div class="row">
				<div class="col m6 s12 mb-1">
					<button class="green btn submitBtnCls submitconfirms" type="submit" name="action">
						Submit
					</button>
				</div>
				<div class="col m4 s12 mb-3">
				 <a href="{{ url()->current() }}" class="waves-effect waves dark btn btn-primary next-step">Reset</a> 
				</div> 
			</div>
		</div>
	</div>

	</div>
{{ Form::close() }}

