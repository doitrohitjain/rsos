@extends('layouts.default') 
@section('content') 
@php
$role_id = @Session::get('role_id');
@endphp

<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        {{-- div_student_top_navigation --}}
                        <div class="card"> @include('elements.student_top_navigation') </div>
                    </div>
                    <div class="col s12 m12 l12" style="background-color: #4f505245;">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{ $page_title; }} 
                                    @php $textColor = "red"; @endphp
									
                                    @if($finalVerifyStatus == "Approved")
                                        @php $textColor = "green"; @endphp
                                    @endif
									@if(@$isShowVerifcationPart)
										<span class="right" style="color:{{ $textColor }}">
											{{ @$finalVerifyStatus }}
										</span>
									@endif
                                </h4>
                                @can('markeligeable')
                                    <div class="col-md-12" style="font-size:20px;">
	                                    <a href="{{ route('student_update_eligible',[Crypt::encrypt(@$masterrecord->enrollment),1]) }}">
                                            <span class="btn btn-warning">Mark Eligible</span>
                                        </a>
                                    </div>
                                @endcan    
								    @php 
                                        $fld='locksumbitted'; 
                                    @endphp
									@if(empty(@$masterrecord->locksubmitted_date) && @$makepaymentchangerequerts == 'true' && @$studentdata->student_change_requests == 2 && @$checkchangerequestsAllowOrNotAllow == 'true')
									<?php 
									$lblForPaymentText = null;
                                    $lblforenrollment = "आवेदन पत्र सत्यापित होने के बाद नामांकन जारी होगा|";
                                        $lblForPaymentText = "कृपया फॉर्म को लॉक और सबमिट करें और <b> लॉक और सबमिट करने के बाद आवेदन फॉर्म शुल्क का भुगतान ऑनलाइन करें</b>। आपका नामांकन शुल्क भुगतान के बाद मिलेगा।(Please lock and submit form and after lock and submit should <b>online pay the application form fees</b>. Your enrollment will generate after a successful fee payment and validation of form by department.) ";
										
										// $lblForPaymentText = "लॉक और सबमिट डेट बंद कर दिया गया है <b>।(Lock  & Submit date has been closed.) </b>";
									?> 
									@elseif(empty(@$masterrecord->locksubmitted_date) && @$makepaymentchangerequerts == 'false' && @$studentdata->student_change_requests == 2 && @$checkchangerequestsAllowOrNotAllow == 'true')
									<?php 
									$lblForPaymentText = null;
                                    $lblforenrollment = "आवेदन पत्र सत्यापित होने के बाद नामांकन जारी होगा|"; 
									$lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और  <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form <b>No need to pay fees</b>.)";
									?> 
									@else
									<?php 
									$lblForPaymentText = null;
                                    $lblforenrollment = "आवेदन पत्र सत्यापित होने के बाद नामांकन जारी होगा|";
									if(empty($masterrecord->locksubmitted_date) && @$masterrecord->category_a != 7 ){
                                        $lblForPaymentText = "कृपया फॉर्म को लॉक और सबमिट करें और <b> लॉक और सबमिट करने के बाद आवेदन फॉर्म शुल्क का भुगतान ऑनलाइन करें</b>। आपका नामांकन शुल्क भुगतान के बाद मिलेगा।(Please lock and submit form and after lock and submit should <b>online pay the application form fees</b>. Your enrollment will generate after a successful fee payment and validation of form by department.) ";
										
										// $lblForPaymentText = "लॉक और सबमिट डेट बंद कर दिया गया है <b>।(Lock  & Submit date has been closed.) </b>";
									?> 
									<?php }else if(empty($masterrecord->locksubmitted_date) && @$masterrecord->category_a == 7 ){
								
									$lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और आप विशेष श्रेणी(". $categorya[$masterrecord->category_a] ."  ) में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category (". $categorya[$masterrecord->category_a] ."  ) so <b>No need to pay fees</b>.)";
									?> 
                                    <?php }  ?>
									@endif
                                    <?php 
                                        if($application_fee <= 0 ){ 
                                            $lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और आप विशेष श्रेणी में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category so <b>No need to pay fees</b>.)";
                                        }
                                    ?>

									<?php if(!empty($lblForPaymentText)){ ?>
										<span style="color:red;font-size:18px;">
											<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblForPaymentText; ?><br/>
                                           
                                        </span>
                                        <span style="color:blue;font-size:18px;">											
                                            <?php echo $lblforenrollment; ?>
                                        </span>
									<?php } ?>
									
									@if(!empty(@$masterrecord->locksubmitted_date) && @$masterrecord->locksumbitted == 1 &&  !empty(@$studentdata->update_change_requests_challan_tid))
									@can('download_student_pdf')
								     <?php $lblPDFLink = "Download Change Request Payment PDF"; ?>
										<h4> 
                                            <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$masterrecord->student_id)) }}">
                                                <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                                <?php echo $lblPDFLink; ?>
                                            </a> 
                                        </h4>
                                    @endcan
                                    @elseif(!empty(@$masterrecord->locksubmitted_date) && @$masterrecord->locksumbitted == 1 && @$changerequeststudentdata->student_update_application == 1)
									<?php $lblPDFLink = "Download Change Request PDF"; ?>
										<h4> 
                                            <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$masterrecord->student_id)) }}">
                                                <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                                <?php echo $lblPDFLink; ?>
                                            </a> 
                                        </h4>
									@else
									<?php 
									$lblPDFLink = null;
									if(!empty(@$masterrecord->locksubmitted_date) && !empty($masterrecord->locksubmitted_date) && (@$masterrecord->fee_paid_amount <= 0 || @$masterrecord->fee_paid_amount == null) ){ ?>
                                        <?php  
                                            //if(@$masterrecord->enrollment){
                                                $lblPDFLink = "Download PDF";
                                           // }
                                            ?>
                                    <?php  } else { ?>
                                        <?php $lblPDFLink = "Download Payment PDF"; ?>
                                    <?php }?> 
                                    @can('download_student_pdf')
                                    @if(!empty(@$masterrecord->locksubmitted_date) && !empty($masterrecord->locksubmitted_date) && @$lblPDFLink )
                                        <h4> 
                                            <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$masterrecord->student_id)) }}">
                                                <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                                <?php echo $lblPDFLink; ?>
                                            </a> 
                                        </h4>
									@endif
                                    @endcan	
								  @endif
								 @if(@$checkchangerequestsAllowOrNotAllow == true && !empty(@$masterrecord->locksubmitted_date) && @$masterrecord->locksumbitted == 1 && @$studentdata->student_change_requests == 2 &&  empty(@$studentdata->update_change_requests_challan_tid) && @$makepaymentchangerequerts == 'true')
									 
									   @if(@$changerequeststreamgatdata->option_val == @$studentdata->exam_month) 
									<span style="color: red;font-size:14px;">
                                                                                       सत्यापन के लिए आवेदन पत्र जमा करने हेतु छात्र को आवेदन पत्र शुल्क का भुगतान करना होगा ( For submitting application form  for verification Student need to pay application form fees)
                                                </span> 
                                                <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                                    <a href="{{ route('change_request_registration_fee',Crypt::encrypt(@$masterrecord->student_id)) }}" style="">
                                                                                         प्रवेश शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें।(Please click here to pay admission fees.)
                                                    <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment For Change</span></a>
                                                </div>
									 @elseif($changerequeststreamgatdata->option_val == 3)
									 <span style="color: red;font-size:14px;">
                                                                                       सत्यापन के लिए आवेदन पत्र जमा करने हेतु छात्र को आवेदन पत्र शुल्क का भुगतान करना होगा ( For submitting application form  for verification Student need to pay application form fees)
                                                </span> 
                                                <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                                    <a href="{{ route('change_request_registration_fee',Crypt::encrypt(@$masterrecord->student_id)) }}" style="">
                                                                                         प्रवेश शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें।(Please click here to pay admission fees.)
                                                    <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment For Change</span></a>
                                                </div>
								      @endif
								
								 @else
								<?php
									 // @dd($feePaymentAllowOrNotStatus);
									// @dd($masterrecord);
									//if any one entry then not show button student_update_application
									if($feePaymentAllowOrNotStatus == 'true' && !empty($masterrecord->locksubmitted_date) && $masterrecord->locksumbitted == 1 &&  @$isMainPaymentButtonShow){
									// if($feePaymentAllowOrNotStatus == 'true'){ 									
									?>  
										
                                        @if($masterrecord->category_a != 7)
                                           
                                            @if($application_fee > 0  && $masterrecord->fee_paid_amount <= 1 && @$studentdata->update_change_requests_challan_tid == null)
												
                                                <span style="color: red;font-size:14px;">
                                                    सत्यापन के लिए आवेदन पत्र जमा करने हेतु छात्र को आवेदन पत्र शुल्क का भुगतान करना होगा ( For submitting application form  for verification Student need to pay application form fees)
                                                </span> 
                                                <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                                    <a href="{{ route('registration_fee',Crypt::encrypt(@$masterrecord->student_id)) }}" style="">
                                                    प्रवेश शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें।(Please click here to pay admission fees.)
                                                    <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment</span></a>
                                                </div>
                                            @endif
                                        @endif
										<?php  } else{ ?>
										<?php  }  ?>
                                            
									 @endif 
                                            
									 
									  
								<!--<div class="col x25 m12 s12"> -->
								<div class="">
								@foreach($master as $secotionFld => $values) 
						
									@php if($values['data'] != null && @$values['seciontLabel']){   @endphp 
									<div class="card">
                                        <div class="card-content invoice-print-area"> 
											<h6>@php echo @$values['seciontLabel'];  @endphp </h6>
											<!--<div class="divider mb-3 mt-3"></div>-->
                                            <div class="row">
											</br>
                                                <div class="col m12 s12">
													<table><tr> 
														@php $counter = 0; @endphp 
														@php if($values['data'] != null){ @endphp 
														@foreach(@$values['data'] as $fld => $lbl) 
														@php $showTr = false;
														if($counter%2 === 0){ $showTr = true; } 
														if($showTr){  echo "</tr><tr>"; } 
														@endphp 
														<td width="20%">@php echo @$lbl['label']; @endphp </td>
														<td width="5%"> @php echo " : "; @endphp </td>
														<td width="20%"> @php echo $lbl['value']; @endphp </td>
														@php $counter++; @endphp 
														@endforeach 
														@php } @endphp 
														</tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="divider mb-3 mt-3"></div>-->
                                    </div>
									@php } @endphp 
									@endforeach 
                                    @if(count($mastertocdetails) > 0) 
										<div class="card card-content">
                                        <div class="card-content invoice-print-area">
                                               <span > TOC Board Details </span>
                                            <div class="row">
                                                    <div class="col m12 s12">
                                                        <table>
                                                        <tr>
                                                            <th>Name Of Board</th>
                                                            <th>Year</th>
                                                            <th>Roll No</th>
                                                        </tr>
														@if(isset($tocdetails))
                                                    @foreach($tocdetails as $value)
                                                    <tr>
                                                            
                                                            <td>{{@$getBoardList[@$value->board]}}</td>
															 @if(!empty(@$value->year_fail))
                                                             <td>{{@$tocpassfail[@$value->year_fail]}}</td>
														     @else
															  <td>{{@$tocpassyear[@$value->year_pass]}}</td>
															 @endif
                                                            <td>{{@$value->roll_no}}</td>
                                                 </tr>
                                                  @endforeach   
												  @endif
                                                    </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                      <div class="card card-content">
                                        <div class="card-content invoice-print-area">
                                               <span > TOC Subjects Details </span>
                                            <div class="row">
                                                    <div class="col m12 s12">
                                                        <table>
                                                        <tr>
                                                            <th>Subjects</th>
                                                            <th>Theory</th>
                                                            <th>Practical</th>
                                                            <th>Total Marks</th>
                                                        </tr>
                                                    @foreach ($mastertocdetails as $value )
                                                    <tr>
                                                            <td>{{@$student_subject_list[$value->subject_id]}}</td>
                                                            <td>{{@$value->theory}}</td>
                                                            <td>{{@$value->practical}}</td>
                                                            <td>{{@$value->total_marks}}</td>
                                                 </tr>
                                                  @endforeach   
                                                    </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    @endif
									@if(@$checkchangerequestsAllowOrNotAllow == true && $studentdata->student_change_requests == 2)
										 @if(@$changerequeststreamgatdata->option_val == @$studentdata->exam_month) 
										@include('elements.change_requerst_locksumbitted')
									     @elseif($changerequeststreamgatdata->option_val == 3)
										 @include('elements.change_requerst_locksumbitted')
									    @endif
									@else
									@if(!$documentErrors) 
										@php
										$is_dgs_student = false;
										if ($studentdata->is_dgs == 1) {
											$is_dgs_student = true;
										}
										@endphp
                                        <div class="card">
                                                @php $fld='locksumbitted'; @endphp 
                                                    @if(empty(@$masterrecord->$fld))
                                            <div class="card-content invoice-print-area">
                                                <div class="row">
                                                    <div class="col m12 s12">
                                                    @php $lbl=' घोषणा (Declaration)'; $fld='locksumbitted'; @endphp 
													@php $lbl1='Declaration'; $fld1='Declaration'; @endphp 
                                                        {{ Form::open(['route' => [request()->route()->getAction()['as'],$estudent_id], 'id' =>$model]) }} 
                                                        {!! Form::token() !!} 
                                                        {!! method_field('PUT') !!} 
															<?php if(@$currentDateAllowOrNotStatus){ ?>
                                                            <p class="mb-1">
                                                                <label>
                                                                    <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                                                    <br>
                                                                    {{ Form::checkbox($fld, null) }}
                                                                    <span>{{@$student_declaration[1]}}
																	@php echo $lbl1.Config::get('global.fresh_form_second_undertaking_msg');@endphp	
                                                                    </span>
																	
                                                                </label><br><br> 
                                                                @include('elements.field_error')
                                                            </p>
															
															
															<p class="mb-1">
                                                                <label>
                                                                    <h8>@php echo $lbl1.Config::get('global.starMark'); @endphp </h8>
                                                                    <br>
                                                                    {{ Form::checkbox($fld1, null) }}
                                                                    <span>
																	@php echo $lbl1.Config::get('global.fresh_form_undertaking_msg'); @endphp</span>
																</label>
																<br><br> 
                                                                @include('elements.field_error')
                                                            </p>
															
														<div class="col m7 s12 mb-3">
                                                            <button class="btn cyan waves-effect waves-light right btn_disabled" type="submit" name="action"> Lock & Submit </button>
                                                        </div> 
														
														<?php } else { ?>
														    <span style="color:red;"> <center><b>Lock and Submit Date Closed.</b></center></span>
														<?php } ?>
														
                                                        {{ Form::close() }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                    <div class="card card-content">
                                        <div class="card-content invoice-print-area" style="color:red;">
                                               <span style="color:green;font-size:20px;font-weight: bold;"> Pending Docuemtns </span>
                                                @foreach(@$documentErrors as $v)
                                                    <div class="row">
                                                        <div class="col m12 s12">
                                                            <table>
                                                                <tr> 
                                                                    @php $link = 'document_details'; @endphp
                                                                    <a data-target="" class="" href="{{ route($link,Crypt::encrypt($student_id)) }}">
                                                                        <span class="dropdown-title3" data-i18n="Persoanl">
                                                                            @php echo $v; @endphp
                                                                        </span>  
                                                                    </a>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
								        </div>
								    </div>	
									@endif
									
  
								</div>
                            </div>
                            <div class="row"></div>
							<br>
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
	<script src="{!! asset('public/app-assets/js/bladejs/preview_details.js') !!}"></script> 
@endsection 
