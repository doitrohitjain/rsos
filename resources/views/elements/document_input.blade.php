@foreach($documentInput as $fld => $lbl)
 
{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
  'enctype' => 'multipart/form-data', "id" => $model . "_" . $fld]) }}
  {!! Form::token() !!}
  {!! method_field('PUT') !!}  
    <div class="row">
		
      <div class="col m12 s12 ">
		@if($fld != 'label')
			<div class="col m5 s8 mb-1">
				<div class="file-path-wrapper">
				  <!--<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" style="font-weight: bold;" disabled>-->
				  <span style="font-size:20px;font-weight: bold;color:#000;border-bottom:1px dotted rgba(0, 0, 0, 0.42)">
				  <b>@php echo $lbl; @endphp</b> 
					@php
						$modelContent = '<h5><center>Valid Document for '. @$lbl . ' </h5></center><br> '. @$docContents[$fld] . '';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls " style="color:blue;" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>
						&nbsp;&nbsp;
					</span>
				  @include('elements.field_error')
				</div>
			</div> 
			<div class="file-field input-field btn col m3 s10 mb-3">
				<span>@php echo $lbl; $fldInputType ="document_type"; $fldInput = "document_input";@endphp</span>
				{!!Form::hidden($fldInputType,"d",['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
				{!!Form::hidden($fldInput,$fld,['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
				{!!Form::file($fld,['type'=>'file',"data-type" => "d" , "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'form-control inputfile test','autocomplete'=>'off']); !!}
			</div>
			<div class="input-field col m2 s10 mb-3">
				@if(!empty(@$master->$fld))
					@php  
						$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$master->$fld)); 
					@endphp			
					<a href="{{ $filePath }} " target="_blank" class='btn btn-ghost-info'>
					   <i class="fa fa-download">download</i>
					</a> 
				@endif
			</div>
		@endif
		
		@php $lblText= @$documentInput['label'][$fld . "_label"];  @endphp
		@if(@$lblText)
			<div class="col m12 s12 mb-1">
				<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
 					@php echo "Note: Valid Document " . $lblText;   @endphp
				</span>
			</div>
		@endif
		 
		 
      </div>  
    </div>  
  {{ Form::close() }}
@endforeach