@extends('layouts.default')
@section('content')
<!-- BEGIN: Page Main-->
    <div id="main">
	  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form Layouts
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
	<div class="card">
    <div class="card-content">
      <p class="caption mb-0"><h6>Edit Required Document <span style="margin-left: 85%;"><a href="{{ route('getpublishaicentermaterial') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
	</div>
  <div class="row">
    <div class="col s12">
      <div id="html-validations" class="card card-tabs">
        <div class="card-content">
          <div id="html-view-validations">
		  {{ Form::open(['url'=>url()->current(),'method'=>'PUT']) }}
				<div class="row">
	                <div class="col m4 s12">
						@php $lbl="प्रवेश प्रकार (Admission type)"; $fld='adm_type'; @endphp 
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field">{!!Form::select($fld,$adm_type,@$verficationData->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'required'=>'true']); !!}
							@include('elements.field_error')	
						</div>
					  </div>
		        	<div class="col m4 s12">
						@php $lbl="कोर्स (Course)"; $fld='course'; @endphp 
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field">{!!Form::select($fld,$course,@$verficationData->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'required'=>'true']); !!}
							@include('elements.field_error')	
						</div>
					</div> 
					<div class="col m4 s12">
						@php $lbl="सत्यापन लेबल (Verification Label)"; $fld='main_document_id'; @endphp 
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field">{!!Form::select($fld,@$verfication_label,$verficationData->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'required'=>'true']); !!}
							@include('elements.field_error')	
						</div>
					</div>

					<div class="col m4 s12">
						@php $lbl="फ़ील्ड आईडीं(Field Id)"; $fld='field_id'; @endphp 
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field">{!!Form::text($fld,@$verficationData->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'required'=>'true']); !!}
							@include('elements.field_error')
						</div>
					</div>
					<div class="col m4 s12">
						@php $lbl="फॉर्म भरी हुई तालिका (Form Filled Table)"; $fld='form_filled_tbl'; @endphp 
						<span class="small_lable">@php echo $lbl ; @endphp </span>
						<div class="input-field">{!!Form::text($fld,@$verficationData->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
							@include('elements.field_error')	
						</div>
					</div>
					<div class="col m4 s12">
						@php $lbl="फॉर्म भरा हुआ संदर्भ (Form Filled Reference)"; $fld='form_filled_ref'; @endphp 
						<span class="small_lable">@php echo $lbl ; @endphp </span>
						<div class="input-field">{!!Form::text($fld,@$verficationData->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
							@include('elements.field_error')
						</div>
					</div>						
					<div class="row">
						<div id="toolbar"></div>
							<div class="col l12 m12 s12">
							<textarea class="form-control ckeditor" id="ckeditorId" name="field_name">{{@$verficationData->field_name}}</textarea>
							</div> 
						</div>
					<div class="col m4 s12">
						@php $lbl="विकल्प स्थति(Status)"; $fld='status'; @endphp 
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field">
		                {!!Form::select($fld,$status,@$verficationData->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'required'=>'true']); !!}
							@include('elements.field_error')	
						</div>
					</div>  
		        </div> 
           </div>


     <div class="row">
			   <div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
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
<script src="{!! asset('public/app-assets/js/bladejs/useradd_details.js') !!}"></script> 
@endsection 
<!-- <link href="{!! asset('public/app-assets/css/quill.snow.css') !!}">
<script src="{!! asset('public/app-assets/js/quill.min.js') !!}"></script>  -->


  



