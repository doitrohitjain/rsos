@extends('layouts.default')
@section('content')
@if($getcurrentsupplementariesdata->course == 10)
	@php $inWhichCondtion = 1;	 @endphp
	@if(@$suppVerifcationData->aicenter_rejected_marksheet_document == 1 || @$suppVerifcationData->department_rejected_marksheet_document == 1 )
		@php
			$inWhichCondtion = 1;
			unset($documentInput['sec_marksheet_doc']);
		@endphp
	@endif
@else  
	@php $inWhichCondtion = 3;	 @endphp
	@if(@$suppVerifcationData->aicenter_rejected_marksheet_document == 3 || @$suppVerifcationData->department_rejected_marksheet_document == 3 )
		@php $inWhichCondtion = 3;	 @endphp
	@elseif(@$suppVerifcationData->aicenter_rejected_marksheet_document == 2 || @$suppVerifcationData->department_rejected_marksheet_document == 2 )
		@php
			$inWhichCondtion = 2;	
			unset($documentInput['sec_marksheet_doc']); 
		@endphp
	@elseif(@$suppVerifcationData->aicenter_rejected_marksheet_document == 1 || @$suppVerifcationData->department_rejected_marksheet_document == 1 )
		@php
			$inWhichCondtion = 1;
			unset($documentInput['marksheet_doc']);
		@endphp
	@endif
@endif 
 
			
			
<div id="main">
<div class="row">
<div class="col s12">
<div class="container">
<div class="seaction">
<div class="col s12 m12 l12">
<div class="col s12 m12 l12" style="">
<div id="Form-advance" class="card card card-default scrollspy">
</div>
<div class="card-content"> 
<div class="col s12 m12 l12">
<div id="Form-advance" class="card card card-default scrollspy">
<div class="card-content">
   <form action="{{ route('supp_verfication_document') }}" enctype="multipart/form-data" method="POST" id="Supplementary">
	@csrf
		<h4 class="card-title">Marksheet Documents</h4>
			<td>
				<span style="color:red;">
					Note: Please upload last valid marksheet.(नोट: कृपया अंतिम मान्य मार्कशीट अपलोड करें।)
				</span>
				<br>
				<span style="color:blue;">
					Please upload document format (jpeg,png,jpg,gif,svg,pdf) and size between 10 kb to 100 kb.
				</span>
			</td>
	  
			{{-- <td>
				<span style="color:red;">
					Note: Please upload last valid marksheet.(नोट: कृपया अंतिम मान्य मार्कशीट अपलोड करें।)
				</span>
			</td> --}}
		<div class="col x25 m12 s12"> 
			<table>
				<tr> 
					<td>
			 
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
						{!!Form::file($fld,['type'=>'file', "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'supp_form_document_field form-control inputfile test','autocomplete'=>'off']); !!}
						 
						<span style="color:green"  class="supp_form_document_value" id= "div_cls_{{ $fld }}"></span>
						<br>
						<br>
					</div> 
						@if($inWhichCondtion == 1)
							@if($getcurrentsupplementariesdata->course == 12)
								@if(@$supplementaryDetails->$fld) 
									<div class="row hide"> 
										<div class="col m12 s12 l12 ">
											<b>Previously uploaded documents for clarification.</b>
											&nbsp;&nbsp;&nbsp;&nbsp;
											@php 
												$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$supplementaryDetails->sec_marksheet_doc)); 
											@endphp
											<a href="{{ $filePath }} " target="_blank" class='file-field input-field'>
												<i class="fa fa-download">Download</i>
											</a> 
										</div>
									</div>   
								@endif  
							@endif  
							
							@if($getcurrentsupplementariesdata->course == 10)
								@if(@$supplementaryDetails->supp_doc) 
									<div class="row hide"> 
										<div class="col m12 s12 l12 ">
											<b>Previously uploaded documents for clarification.</b>
											&nbsp;&nbsp;&nbsp;&nbsp;
											@php 
												$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$supplementaryDetails->supp_doc)); 
											@endphp
											<a href="{{ $filePath }} " target="_blank" class='file-field input-field'>
												<i class="fa fa-download">Download</i>
											</a>  
										</div>
									</div>   

								@endif 
							@endif 
							
						@elseif($inWhichCondtion == 2)
							  @if(@$supplementaryDetails->$fld) 
								<div class="row hide"> 
									<div class="col m12 s12 l12 ">
										<b>Previously uploaded documents for clarification.</b>
										&nbsp;&nbsp;&nbsp;&nbsp;
										@php 
											$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$supplementaryDetails->supp_doc)); 
										@endphp
										<a href="{{ $filePath }} " target="_blank" class='file-field input-field'>
											<i class="fa fa-download">Download</i>
										</a> 
									</div>
								</div>  
							@endif 
						 @elseif($inWhichCondtion == 3)
							@if(@$supplementaryDetails->$fld) 
								<div class="row hide"> 
									<div class="col m12 s12 l12 ">
										<b>Previously uploaded documents for clarification.</b>
										&nbsp;&nbsp;&nbsp;&nbsp;
										@php 
											$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$supplementaryDetails->$fld));
										@endphp
										<a href="{{ $filePath }} " target="_blank" class='file-field input-field btn'>
											<i class="fa fa-download">Download</i>
										</a> 
									</div>
								</div> 
							 @endif 
						 @endif 
					
						{!! Form::hidden('sec_marksheet_doc_hidden',@$supplementaryDetails->sec_marksheet_doc,['type'=>'text','id'=>'sec_marksheet_doc_hidden','value'=>@$supplementaryDetails->sec_marksheet_doc]); !!}
						{!! Form::hidden('marksheet_doc_hidden',@$supplementaryDetails->marksheet_doc,['type'=>'text','id'=>'marksheet_doc_hidden','value'=>@$supplementaryDetails->marksheet_doc]); !!}
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
				</td>
				</tr>
			  </table>
		</div>
		<div class="row"><br><br><br><br><br><br><br><br><br>
		<div class="col m10 s12 mb-3">
			<button class="btn cyan waves-effect waves-light right supp_form_submit" type="submit "  name="action"> Upload Image</button>
		</div>
		<div class="col m2 s10 mb-3">
		   <a class="btn waves-effect waves-light form_reset" href="{{route('supp_verfication_document')}}">Reset</a>
			
		</div>
		
	</div>
	</div>
		</form>
</div>
</div>
</div>
<div class="row"></div>
</div>
</div>
</div> 
</div>
</div>
</div>
</div>
</div> 

@endsection 
