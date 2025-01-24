@php //dd($documents_verification_arr); @endphp
@foreach($documents_verification_arr as $key => $value)
@php
	
	//dd($verificationLabels);
	
	$lbl = @$verificationLabels[$key]['hindi_name'];
	$fld = @$verificationLabels[$key]['name']; 
	
	$fld = str_replace("doc_", "", $fld);
	$remarks = '<span style="font-size:20px;">' . @$lbl . '</span>';
	 
	$liItems = array_map(function($item) {
		return "<li>{$item}</li>";
	}, @$verificationLowerLabels[$key]); 
	$liString = implode("", $liItems); 
	$remarks .=   "<span style='font-size:14px;'><ol>{$liString}</ol></span>";


@endphp

{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
  'enctype' => 'multipart/form-data', "id" => $model . "_" . $fld]) }}
  {!! Form::token() !!}
  {!! method_field('PUT') !!}
    <div class="row">
      <div class="col m12 s12 ">
		@if($fld != 'label')
			@php 
			// echo $fieldBaseName . $fld . "_is_verify_remarks";
			// dd($getroleid); 
			 @endphp
			<div class="col m5 s8 mb-1">
				<div class="file-path-wrapper">
				  <!--<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" style="font-weight: bold;" disabled>-->
				  	<span style="font-weight: bold;color:#000;border-bottom:1px dotted rgba(0, 0, 0, 0.42)">
				  	<b>@php echo $lbl; @endphp</b></span>
					@php
						$modelContent = '<h4><center>Clarification Reason : </center><br>'. @$remarks . ' </h4>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2" title="Clarification Reason"> info_outline </i></span> 
				  	@include('elements.field_error')
				</div>
			</div> 
			<div class="input-field col m1 s10 mb-3">
				
			</div>
			<div class="file-field input-field btn col m3 s10 mb-3">
				<span>@php echo $lbl; $fldInputType ="document_type"; $fldInput = "document_input";
					$val = "d";
					if($fld == "photograph" || $fld == "signature"){
						$val = "i";
					}
				@endphp</span>
				{!!Form::hidden($fldInputType,$val,['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
				{!!Form::hidden($fldInput,$fld,['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
				{!!Form::file($fld,['type'=>'file',"data-type" => $val , "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'form-control inputfile test','autocomplete'=>'off']); !!}
			</div>
			
			<div class="input-field col m2 s10 mb-3">
				
			   @php $path = public_path($studentDocumentPath . "" . @$studentDocumentVerificaitonData->$fld); 
			 	
			   @endphp
				@if(!empty(@$studentDocumentVerificaitonData->$fld))
					@php
						$filePath = url(('public/'.$studentDocumentPath . "/" . @$studentDocumentVerificaitonData->$fld));  
					@endphp
					@if(file_exists($path))	
					
					<a href="{{ $filePath }} " target="_blank" download class='btn btn-ghost-info'>
					   <i class="fa fa-download">download</i> 
					</a> 
					@else
						{{ "Not Found" }} 
					@endif	
				@endif
			</div>
		@endif
		</div>  
		@php $lblText= @$documentInput['label'][$fld . "_label"];  @endphp
		@if(@$lblText)
			<div class="col m12 s12 mb-1">
				<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
 					@php echo "Note: Valid Document " . $lblText;   @endphp
				</span>
			</div>
		@endif
		 
    </div>  
  {{ Form::close() }}
@endforeach