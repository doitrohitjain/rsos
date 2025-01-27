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
                        <p class="caption mb-0">
                        <h6>
                        Practical Examiner Form <span style="margin-left: 85%;"><a href="{{ route('practicalexaminer') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
                        <h6>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations">
                                    {!! Form::open(array('route' => ['practicalexamineredit', Crypt::encrypt($user->id)],'method'=>'PATCH','id'=>'practicalexamineradd')) !!}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                        <div class="input-field col s6">
                                            @php $lbl='एसएसओआईडी (SSOID)'; $fld='ssoid'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='पूरा नाम (Full Name)'; $fld='name'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control txtOnly','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='ईमेल (Email)'; $fld='email'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='मोबाइल(Mobile)'; $fld='mobile'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user->$fld,['type'=>'text','minlength'=>'10','maxlength'=>'10','class'=>'num form-control','autocomplete'=>'off']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='विद्यालय का नाम(School Name)'; $fld='college_name'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
										
										@php if($isAdminStatus==true){ @endphp
										 <div class="input-field col s6">
											@php $lbl='ज़िला (District)'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
											<span class="small_lable">
												@php echo $lbl .Config::get('global.starMark'); @endphp </span>
											<div class="input-field">
											{!! Form::select($fld,@$district_list,@$user->$fld,['class' => 'select2 browser-default form-control center-align district_id','placeholder' => $placeholder,'id'=>'district_id','style' => 'text-align: left','required'=>'required']) !!}
											@include('elements.field_error')
											</div>
										</div>
				
										 <div class="input-field col s6">
											@php $lbl='जिला शिक्षा अधिकारी (Deo Name)'; $placeholder = "Select ". $lbl; $fld='deo_id'; @endphp
                                            <span class="small_lable">
												@php echo $lbl .Config::get('global.starMark'); @endphp 
											</span>
                                            {!! Form::select($fld,@$deo_list,@$selected_deo,['class' => 'select2 browser-default form-control center-align deo_id','placeholder' => $placeholder,'id'=>'deo_id','required'=>'required']) !!}
											@include('elements.field_error')
                                        </div>
										@php } else { @endphp
										<input type="hidden" name='district_id' value='@php echo @Auth::user()->district_id; @endphp' id='district_id'>
										<input type="hidden" name='deo_id' value='@php echo @Auth::user()->id; @endphp' id='deo_id'>
										@php }  @endphp
                                        
										<div class="row">
                                            <div class="col m10 s12 mb-3">
                                                <!--<button class="btn cyan waves-effect waves-light right" type="reset">
                                                <i class="material-icons right">clear</i>Reset
                                                </button>-->
												<a href="{{ route('practicalexamineredit',Crypt::encrypt($user->id)) }}" class="btn cyan waves-effect waves-light right">Reset</a>
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
	<script src="{!! asset('public/app-assets/js/bladejs/practical/practicalexamineredit.js') !!}"></script> 
@endsection 

