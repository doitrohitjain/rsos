<div class="row">
	@foreach($imageInput as $fld => $lbl)
	{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
	'enctype' => 'multipart/form-data', "id" => $model . "_" . $fld]) }}
	{!! Form::token() !!}
	{!! method_field('PUT') !!}  
    <div class="col m6 s12 file-field input-field">
		<div class="btn float-right">
			<span>@php echo $lbl; $fldInputType ="document_type"; $fldInput = "document_input";@endphp</span>
			{!!Form::hidden($fldInputType,"i",['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
			{!!Form::hidden($fldInput,$fld,['type'=>'hidden', 'class'=>'form-control inputfile','autocomplete'=>'off']); !!} 
			{!!Form::file($fld,['type'=>'file',"data-type" => "i" , "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
		</div>
      
		<div class="file-path-wrapper">
			<span style="text-align:center;">
				@if(!empty(@$master->$fld))
					@php 
					$dump_image = null;
				    if($fld == 'photograph'){
						$dump_image='users1.png';	
					}else if($fld == 'signature'){
						$dump_image = 'signature1.png';
					}
					$image_path=public_path($studentDocumentPath . "/" . @$master->$fld);
					if(file_exists($image_path)){
						@$image_path =url('public/'.$studentDocumentPath . "/" . @$master->$fld);
					}else{
						$image_path=url('public/app-assets/images/'.$dump_image);
					}
				@endphp
					<img src="{{ @$image_path }}" width="90" height="90" alt="" title=""  />
				@endif
				@if(empty(@$master->$fld))
					<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" disabled>
				@endif
				@include('elements.field_error')
			</span>
		</div>
    </div>  
  {{ Form::close() }}
@endforeach
</div>
			