@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="col s12 m12 l12">
						<div class="card"> 
						</div>
					</div>
					<div class="col s12 m12 l12"> 
						<div id="Form-advance" class="card card card-default scrollspy">
							<div class="card-content">
								<h4 class="card-title">{{ $page_title; }} </h4> 
								<div class="row">
									{{ Form::open(['url'=>url()->current(), 
									'enctype' => 'multipart/form-data', "id" => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}  
									<div class="col m6 s12 file-field input-field">
										<div class="btn float-right">
											<span>@php $lbl ="upload Document";
											$fld ="upload_file";
											echo $lbl; $fldInputType ="document_type"; $fldInput = "document_input";@endphp</span>
											{!!Form::file($fld,['type'=>'file',"data-type" => "i" , "data-formId" => $model . "_" . $fld, "id" => $fld, 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
										</div>
										<div class="file-path-wrapper">
										<span style="text-align:center;">
											<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" disabled>
											@include('elements.field_error')
										</span>
			
									</div>
		
								</div>  
	<div class=" col m5 s11">
			@php $lbl='enter document Upload Path'; $fld='document_path'; @endphp
			<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
			<div class="input-field">{!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control  sso_id','id'=>'examiner_ssoid','placeholder'=>'Enter Document Path','autocomplete'=>'off']); !!}
			@include('elements.field_error')
			</div>
		</div>
</div>
								
								
								<div class="col m12 s12 mb-1">
									<!--<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
										Note: The document of minimum 50 kb and maximum 500 kb size should be uploaded in pdf/jpeg/png/gif file type. <span class="starmark" style="color:red;"> *</span>
									</span>-->
								</div>
							</div>
							<div class="row">
								<div class="col m11 s12 mb-1">
								<button type="submit" class="btn cyan waves-effect waves-light right show_confirm  ">submit</button>
								{{ Form::close() }}
								</div> 
							</div>
							<br>
						</div>
					</div>
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 