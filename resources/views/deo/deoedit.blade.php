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
                            Update {{ $title }} <span style="margin-left: 85%;"><a href="{{ route('deo') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
                        <h6>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations">
                                    {!! Form::open(array('route' => ['deoedit', Crypt::encrypt($user_data->id)],'method'=>'PATCH','id'=>'practicaldeoedit')) !!}
                                    {!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									
									<div class="row">
                                        <div class="input-field col s6">
                                            @php $lbl='एसएसओआईडी (SSOID)'; $fld='ssoid'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user_data->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='पूरा नाम (Full Name)'; $fld='name'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user_data->$fld,['type'=>'text','class'=>'name form-control txtOnly','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='ईमेल (Email)'; $fld='email'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user_data->$fld,['type'=>'text','class'=>'email form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='मोबाइल(Mobile)'; $fld='mobile'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$user_data->$fld,['type'=>'text','minlength'=>'10','maxlength'=>'10','class'=>'mobile num form-control','autocomplete'=>'off','required'=>'required']); !!}
                                            @include('elements.field_error')
                                        </div> 
                                        <div class="input-field col s6">
											@php $lbl='चयन जिला (district):'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
											{!! Form::select($fld,$district_list, @$user_data->$fld, ['class' => 'form-control district_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
											@include('elements.field_error')
										</div>
                                        <div class="row">
                                            <div class="col m10 s12 mb-3">
                                                <!--<button class="btn cyan waves-effect waves-light right" type="reset">
                                                <i class="material-icons right">clear</i>Reset
                                                </button>-->
												<a href="{{ route('deoedit',Crypt::encrypt($user_data->id)) }}" class="btn cyan waves-effect waves-light right">Reset</a>
                                            </div>
                                            <div class="col m2 s12 mb-3">
                                                <button class="btn cyan waves-effect waves-light right submit_dsiabled" type="submit" name="action">Submit
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
	<script src="{!! asset('public/app-assets/js/bladejs/practical/practicaldeoedit.js') !!}"></script> 
@endsection 

