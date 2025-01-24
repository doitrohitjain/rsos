@extends('layouts.default')
@section('content')
    <div id="main">
      <div class="row">
	  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Add Bank</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="{{route('bank_masters.index')}}">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Add Bank</a>
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
	 <h6><a href="{{route('bank_masters.index')}}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Add Bank<h6>
	 </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
            <h4 class="card-title">Form Add Bank</h4>
            {{ Form::open(['url'=>url()->current(), 'method' => 'post','id' => $model]) }}
            {!! Form::token() !!}
		  	<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
            <div class="row">
                <div class="col m4 s12">
					@php $lbl="Bank Id"; $fld='BANK_ID'; @endphp 
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control ','required' => 'required','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
					</div>
				</div>
				
				<div class="col m4 s12">
					@php $lbl="Bank Name"; $fld='BANK_NAME'; @endphp 
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','required' => 'required','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
					</div>
				</div>

                <div class="col m4 s12">
					@php $lbl="Bank Name Hindi"; $fld='BANKNAME_MANGAL'; @endphp 
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','required' => 'required','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
					</div>
				</div>
				<div class="col m4 s12">
					@php $lbl="IFSC Code"; $fld='IFSC_CODE'; @endphp 
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','required' => 'required','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
					</div>
				</div>
				<div class="col m4 s12">
				 @php $lbl="MICR"; $fld='MICR'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required' => 'required','placeholder' => $lbl]); !!}
					 @include('elements.field_error')	
					</div>
				</div>
            </div>
            <div class="row">
                <div class="col m4 s12">
					@php $lbl="Bank Branch id"; $fld='BANK_BRANCH_ID'; @endphp 
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','required' => 'required','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
					</div>
				</div>
				<div class="col m4 s12">
				 @php $lbl="Branch Name"; $fld='BRANCH'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','required' => 'required','class'=>'form-control ','autocomplete'=>'off','placeholder' => $lbl]); !!}
					 @include('elements.field_error')	
					</div>
				</div>
                <div class="col m4 s12">
				 @php $lbl="Branch Name Hindi"; $fld='BRANCH_MANGAL'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required' => 'required','placeholder' => $lbl]); !!}
					 @include('elements.field_error')	
					</div>
				</div>

				<div class="col m4 s12">
				 @php $lbl="Branch Address"; $fld='BRANCH_ADDRESS'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">{!!Form::textarea($fld,null,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required' => 'required','placeholder' => $lbl]); !!}
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
			  {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection


