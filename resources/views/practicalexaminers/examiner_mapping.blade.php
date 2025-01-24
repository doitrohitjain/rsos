@extends('layouts.default')
@section('content')
<!-- BEGIN: Page Main-->
<div id="main">
    <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
        <!-- Search for small screen-->
        <div class="container">
            <div class="row">
                <div class="col s12 m6 l6">
                    <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
                </div>
                <div class="col s12 m6 l6 right-align-md">
                    <ol class="breadcrumbs mb-0"> 
                        @foreach($breadcrumbs as $v)
                            <li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
                        @endforeach 
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
                            {{ $title }} <span style="margin-left: 85%;"><a href="{{ route('practicalexaminer') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
                        <h6>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations"> 
								{{ Form::open(['route' => [request()->route()->getAction()['as'], $e_user_id],'method'=>'POST', 'id' => 'PracticalExaminerForm','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                        <div class="input-field col s6"> 
                                            @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$course_dropdown, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										<div class="input-field col s6"> 
                                            @php $lbl='परीक्षा केंद्र (Examination Center)';  $placeholder = "Select ". $lbl; $fld='examcenter_detail'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$examcenter_datails_dropdown, @$master->$fld, ['class' => 'examcenter_detail form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										<div class="input-field col s6"> 
                                            @php $lbl='विषय (Subject)';  $placeholder = "Select ". $lbl; $fld='subject'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$subjects_dropdown, @$master->$fld, ['class' => 'subject form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='एसएसओआईडी (SSOID)'; $fld='ssoid'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$practical_examiner_data->$fld,['type'=>'text','class'=>'ssoid form-control','autocomplete'=>'off','required'=>'required','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='पूरा नाम (Full Name)'; $fld='examiner_name'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$practical_examiner_data->name,['type'=>'text','class'=>'examiner_name form-control txtOnly','autocomplete'=>'off','required'=>'required','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='ईमेल (Email)'; $fld='email'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$practical_examiner_data->$fld,['type'=>'text','class'=>'email form-control','autocomplete'=>'off','required'=>'required','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="input-field col s6">
                                            @php $lbl='मोबाइल(Mobile)'; $fld='mobile'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$practical_examiner_data->mobile,['type'=>'text','minlength'=>'10','maxlength'=>'10','class'=>'mobile num form-control','autocomplete'=>'off','required'=>'required','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
                                        </div>
                                        <div class="row">
                                            <div class="col m10 s12 mb-3">
                                                <!--<button class="btn cyan waves-effect waves-light right" type="reset">
                                                <i class="material-icons right">clear</i>Reset
                                                </button>-->
												<a href="{{ route('examiner_mapping_practical_list',$e_user_id) }}" class="btn cyan waves-effect waves-light right">Reset</a>
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
	<script src="{!! asset('public/app-assets/js/bladejs/practical/examiner_mapping.js') !!}"></script> 
@endsection 
