@extends('layouts.default') 
@section('content') 
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        {{-- div_student_top_navigation --}}
                        <div class="card"> @include('elements.student_top_navigation') </div>
                    </div>
                    <div class="col s12 m12 l12" style="background-color: #4f505245;'">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{ $page_title; }}</h4>
								    @php 
                                        $fld='locksumbitted'; 
                                    @endphp 

									<?php 
                                    
									$lblForPaymentText = null;
                                     
									if(empty($masterrecord->locksubmitted_date) && $masterrecord->category_a != 7 ){
										$lblForPaymentText = "कृपया फॉर्म को लॉक और सबमिट करें और <b> लॉक और सबमिट करने के बाद आवेदन फॉर्म शुल्क का भुगतान ऑनलाइन करें</b>।(Please lock and submit form and after lock and submit should <b>online pay the application form fees</b>.)";
									?> 
									<?php }else if(empty($masterrecord->locksubmitted_date) && $masterrecord->category_a == 7 ){
									
									$lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और आप विशेष श्रेणी(". $categorya[$masterrecord->category_a] ."  ) में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category (". $categorya[$masterrecord->category_a] ."  ) so <b>No need to pay fees</b>.)";
									?>
										
                                    <?php }  ?>

                                    <?php 
                                        if($application_fee <= 0 ){ 
                                            $lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और आप विशेष श्रेणी में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category so <b>No need to pay fees</b>.)";
                                        }
                                    ?>

									<?php if(!empty($lblForPaymentText)){ ?>
										<span style="color:red;font-size:18px;">
											<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblForPaymentText; ?>
										</span>
									<?php } ?>
                                    <?php 
									
									if(!empty(@$masterrecord->locksubmitted_date) && !empty($masterrecord->locksubmitted_date) && @$masterrecord->fee_paid_amount <= 0){ ?>
                                        <?php $lblPDFLink = "Download PDF"; ?>
                                    <?php  } else { ?>
                                        <?php $lblPDFLink = "Download Payment PDF"; ?>
                                    <?php }?> 

                                    @if(!empty(@$masterrecord->locksubmitted_date) && !empty($masterrecord->locksubmitted_date) )
                                        <h4> 
                                            <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$masterrecord->student_id)) }}">
                                                <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                                <?php echo $lblPDFLink; ?>
                                            </a> 
                                        </h4>
									@endif
                                    
									<?php
									// @dd($feePaymentAllowOrNotStatus);
									// @dd($masterrecord);
									if($feePaymentAllowOrNotStatus == 'true' && !empty($masterrecord->locksubmitted_date) && $masterrecord->locksumbitted == 1){ 
									// if($feePaymentAllowOrNotStatus == 'true'){ 									
									?> 
                                        @if($masterrecord->category_a != 7)
                                            @if($application_fee > 0  && $masterrecord->fee_paid_amount <= 1 )
                                                <span style="color: red;font-size:14px;">
                                                    <b>छात्र से </b>:- मोबाइल पर प्राप्त भुगतान लिंक के रूप में आवेदन पत्र शुल्क का भुगतान करने के लिए कहें| (<b>Ask to student </b>:- to pay application form fee as received payment link on mobile.)
                                                </span> 
                                                <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                                    <a href="{{ route('registration_fee',Crypt::encrypt(@$masterrecord->enrollment)) }}" style="">
                                                    प्रवेश शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें।(Please click here to pay admission fees.)
                                                    <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment</span></a>
                                                </div>
                                            @endif
                                        @endif
										<?php  } else{ ?>
										<?php  }  ?>
                                            
									 
									  
								<!--<div class="col x25 m12 s12"> -->
								<div class="">
								@foreach($master as $secotionFld => $values) 
									@php if($values['data'] != null){   @endphp 
									<div class="card">
                                        <div class="card-content invoice-print-area"> 
											@php echo $values['seciontLabel'];  @endphp 
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
														<td width="20%">@php echo $lbl['label']; @endphp </td>
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
                                    @if(!$documentErrors) 
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
															<?php if($currentDateAllowOrNotStatus){ ?>
                                                            <p class="mb-1">
                                                                <label>
                                                                    <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                                                    <br>
                                                                    {{ Form::checkbox($fld, null) }}
                                                                    <span>{{@$student_declaration[1]}}
																		<li> मै  माध्यमिक स्तर  के  पाठ्यक्रम पढने  में समर्थ  हूँ | I am able to pursue secondary level course.</li>
																		<li>मेने  विवरणिका में दी गई योग्यता, शर्ते  पढकर  समझ ली है में इसके योग्य हूँ |  I am eligible for Registration according to rules of RSOS.</li>
																		<li>मेने  सभी जरुरी सूचनाये और प्रमाण पत्र सही सही दिए है | में जानता हूँ कि यदि यह सूचनाये गलत या भ्रम में डालने वाली होती है तो राजस्थान स्टेट ओपन स्कूल द्वारा मेरे उम्मीदवारी समाप्त की जा सकती है |  I have furnished the necessary information/documents correctly. I understand that my candidature is liable to be cancelled by RSOS in the event of this information is found incorrect or misleading.</li>
																		<li> में आरएसओएस  के सभी नियमो का पालन करूँगा और सन्दर्भ केंद्र के अनुशासन  और मर्यादा को बनाये रखूँगा |   I shall be abide by all the rules and regulation of RSOS and shall maintain discipline and decorum of AI.</li>
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
																	प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्टियों की जाँच सावधानीपूर्वक संलग्न प्रमाण-पत्रों द्वारा कर ली गई है तथा समस्त प्रविष्टियां सही पाई गई एवं अभ्यर्थी आवेदित श्रेणी तथा पाठ्यक्रम के लिए आर.एस.ओ.एस. के नियमानुसार पात्र है। आवेदन पत्र मय दस्तावेज सत्यापित कर आर.एस.ओ.एस. कार्यालय भिजवाये जा रहे है।</span>
																</label>
																<br><br> 
                                                                @include('elements.field_error')
                                                            </p>
															
														<div class="col m7 s12 mb-3">
                                                            <button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Lock & Submit </button>
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
