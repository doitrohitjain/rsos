<?php
use App\Component\ThoeryCustomComponent; 
?>
@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
							<h6><a href="{{ route('printduplicatemarksheetcertificate',Crypt::encrypt(@$pastdata->ENROLLNO)) }}" class="btn btn-xs btn-info right">Download Marksheet And Certificate</a></h6>  
                            <h6>{{$title}}<h6>
	                    </div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{$title}}</h4>
									{{ Form::open(['url'=>url()->current(),'id'=>'updatepastdatasub']) }}
									{{ method_field('PUT') }}
									<div class="row">
                                        <input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
										<div class="input-field col m3 s12">
											@php $lbl='उपस्थिति पंजी (Enrollment)'; $fld='ENROLLNO'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                            {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                            @include('elements.field_error')
										</div>
									</div>
                                    @if(!empty($pastdata->EX_SUB1))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB1'; 
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]);
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM1'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM1'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                            @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM1'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst1'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT1'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES1';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif
                                    @if(!empty($pastdata->EX_SUB2))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB2';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM2'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM2'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM3'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst2'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT2'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES2';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($pastdata->EX_SUB3))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB3';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM3'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM3'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM3'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif

                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst3'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT3'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES3';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($pastdata->EX_SUB4))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB4';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM4'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM4'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM4'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif

                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst4'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT4'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES4';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif


                                    @if(!empty($pastdata->EX_SUB5))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB5';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM5'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM5'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM5'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif

                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst5'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT5'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES5';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif


                                    @if(!empty($pastdata->EX_SUB9))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB9';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM6'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM6'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM6'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif

                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst6'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT6'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES6';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif
                                    

                                    @if(!empty($pastdata->EX_SUB7))
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='विषय कोड (Subject Code)'; $fld='EX_SUB7';
                                                $prasub=ThoeryCustomComponent::_getSubjectDetail(@$subjectCodeIds[$pastdata->$fld]); @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$subjects[@$subjectCodeIds[$pastdata->$fld]],['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='FTM7'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            @if($prasub->practical_type==1)
                                                <div class="input-field col m4 s12">
                                                    @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM7'; @endphp
                                                    <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                    {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                    @include('elements.field_error')
                                                </div>
                                                @else
                                            @php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='FPM7'; @endphp
                                                   
                                                    {!!Form::hidden($fld,999,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'required']); !!}
                                                    @include('elements.field_error')
                                            @endif

                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='fst7'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='कुल मार्क (Total Marks)'; $fld='FTOT7'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='अंतिम परिणाम Final Result'; $fld='FRES7';
                                                
                                                if($pastdata->$fld=='P'){
                                                    $pastdata->$fld=1;
                                                }
                                                
                                                @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::select($fld,$result,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                    @endif



                               
                                        <div class="row">
                                            <div class="input-field col m4 s12">
                                                @php $lbl='Final Result'; $fld='RESULT';@endphp
                                               
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,$pastdata->$fld,['type'=>'text','class'=>'form-control num $pastdata->$fld','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            <div class="input-field col m4 s12">
                                                @php $lbl='Total Marks'; $fld='TOTAL_MARK'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                            
                                            <div class="input-field col m4 s12">
                                                @php $lbl='Percentage'; $fld='Percentage'; @endphp
                                                <h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
                                                {!!Form::text($fld,@$pastdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off']); !!}
                                                @include('elements.field_error')
                                            </div>
                                        </div>
                                 
									<div class="row">
										<div class="col m9 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action">Submit 
												<i class="material-icons right"></i>
											</button>
										</div>
										<div class="col m2 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange" type="reset">
												<i class="material-icons right">clear</i>Reset
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
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/updatepastsubdata.js') !!}"></script> 
@endsection 