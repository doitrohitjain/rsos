@if(@$documentInput)
	@foreach($documentInput as $fld => $lbl)
		<div class="row"> 
			<div class="col m12 s12 ">
				@if($fld != 'label')
					<div class="col m5 s8 mb-1">
						<div class="file-path-wrapper">
						<!--<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" style="font-weight: bold;" disabled>-->
						<span style="font-size:20px;font-weight: bold;color:rgba(0, 0, 0, 0.42);border-bottom:1px dotted rgba(0, 0, 0, 0.42)">
						<b>@php echo $lbl; @endphp</b></span>
						@include('elements.field_error')
						</div>
					</div>
					<div class="file-field input-field btn col m3 s10 mb-3">
						 
						<span>@php echo 'Upload'; $fldInputType ="document_type"; $fldInput = "document_input";@endphp</span>
						{!!Form::file($fld,['type'=>'file', "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'supp_form_document_field  form-control form_doc_input inputfile test' . ' ' . $fld,'autocomplete'=>'off']); !!}
						<br> 
						<div style="color:green"  class="supp_form_document_value" id= "div_cls_{{ $fld }}"></div>
						<br>
						<br>
					</div>
				 
					@if(@$fld == "sec_marksheet_doc" && @$master->course == 12)
						@if(!empty(@$supplementaryDetails->sec_marksheet_doc))
							
							<div class="input-field col m2 s10 mb-3">
								@php  
									$filePath = route('supp_download',[@$master->id  , @$supplementaryDetails->sec_marksheet_doc]); 
								@endphp
								<a href="{{ $filePath }} " target="_blank" class='btn btn-ghost-info'>
									<i class="fa fa-download">Download</i>
								</a>
								
								<!--
								<?php if(!empty(@$supplementaryDetails->sec_marksheet_doc)){ ?>
								<span style="color:green"><?php echo @$supplementaryDetails->sec_marksheet_doc; ?></span>
								<?php } ?>
								-->
							</div>
						@endif
					@endif

					@if(@$fld == "marksheet_doc" && !empty(@$supplementaryDetails->marksheet_doc))
						<div class="input-field col m2 s10 mb-3">
							@php  
								$filePath = route('supp_download',[@$master->id  , @$supplementaryDetails->marksheet_doc]); 	
								    
								
								
							@endphp
							<a href="{{ $filePath }} " target="_blank" class='btn btn-ghost-info'>
								<i class="fa fa-download">Download</i>
							</a>
							
							<!--
							<?php if(!empty(@$supplementaryDetails->marksheet_doc)){ ?>
							<span style="color:green"><?php echo @$supplementaryDetails->marksheet_doc; ?></span>
							<?php } ?>
							-->
						</div>
					@endif
					
					@if(@$master->course == 12)
						{!! Form::hidden('sec_marksheet_doc_hidden',@$supplementaryDetails->sec_marksheet_doc,['type'=>'text','id'=>'sec_marksheet_doc_hidden','value'=>@$supplementaryDetails->sec_marksheet_doc]); !!}
					
					{!! Form::hidden('size_sec_marksheet_doc_hidden',0,['type'=>'text','id'=>'size_sec_marksheet_doc_hidden','value'=>'0']); !!}
					
					@endif
					

{!! Form::hidden('course',@$master->course,['type'=>'text','id'=>'course','value'=>@$master->course]); !!}


					{!! Form::hidden('marksheet_doc_hidden',@$supplementaryDetails->marksheet_doc,['type'=>'text','id'=>'marksheet_doc_hidden','value'=>@$supplementaryDetails->marksheet_doc]); !!}
					
					{!! Form::hidden('size_marksheet_doc_hidden',0,['type'=>'text','id'=>'size_marksheet_doc_hidden','value'=>'0']); !!}
					
				@endif
				
				@php $lblText= @$documentInput['label'][$fld . "_label"];  @endphp
				@if(@$lblText)
					<div class="col m12 s12 mb-1">
						<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
							@php echo "Note:  " . $lblText;   @endphp
						</span>
					</div>
				@endif 
			</div>  
		</div>  
	@endforeach
@endif

