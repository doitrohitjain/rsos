@extends('layouts.default') 
@section('content') 


<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        <div class="card"> @include('elements.marsheetcorrection_top_navigation')</div>
                    </div>
                    <div class="col s12 m12 l12" style="background-color: #4f505245;">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{ $page_title; }} </h4>
                                      
								    @php 
                                        $fld='locksumbitted';  
                                    @endphp 

									<?php 
									$lblForPaymentText = null;
									if(empty($mmr_data->locksubmitted_date)){
                                        $lblForPaymentText = "कृपया फॉर्म को लॉक करें और सबमिट करें और लॉक और सबमिट करने के बाद संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन के लिए ऑनलाइन भुगतान करें।</b>।(Please lock and submit form and after lock and submit should <b>online pay for the Revised/Duplicate Marksheet/Migration </b>.) ";
										
										// $lblForPaymentText = "लॉक और सबमिट डेट बंद कर दिया गया है <b>।(Lock  & Submit date has been closed.) </b>";
									}?> 

                                    <?php 
                                        if($mmr_data->total_fees <= 0 ){ 
                                            $lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें</b>।(Please lock and submit form and pay fees</b>.)";
                                        }
                                    ?>

									<?php if(!empty($lblForPaymentText)){ ?>
										<span style="color:red;font-size:18px;">
											<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblForPaymentText; ?>
										</span>
									<?php } ?>
                                    <?php 
									
									if(!empty(@$mmr_data->locksubmitted_date) && !empty($mmr_data->locksubmitted_date) && @$mmr_data->fee_paid_amount <= 0 ){ ?>
                                        <?php 
                                            $lblPDFLink = "Download PDF";
                                            ?>
                                    <?php  } else { ?>
                                        <?php $lblPDFLink = "Download Payment PDF"; ?>
                                    <?php }
									
									
									?> 
                                    <!-- @can('download_reval_student_pdf') -->
                                   
                                    <!-- @endcan -->
									
									@if(!empty(@$mmr_data->locksubmitted_date) && !empty($mmr_data->locksubmitted_date) && @$lblPDFLink )
                                        <h4> 
                                            <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('marksheet_generate_student_pdf',Crypt::encrypt([@$mmr_data->id])) }}">
                                                <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                                <?php echo $lblPDFLink; ?>
                                            </a> 
                                        </h4>
									@endif
									
									<?php
									//  @dd($feePaymentAllowOrNotStatus);
									// @dd($masterrecord);
									if(@$lockandsubmittedallowornot  && !empty($mmr_data->locksubmitted_date) && $mmr_data->locksumbitted == 1){ 
									// if($feePaymentAllowOrNotStatus == 'true'){ 									
									?>  
                                        
                                            @if(@$mmr_data->fee_paid_amount <= 1 )
                                                <span style="color: red;font-size:14px;">
                                                    <b>छात्र से </b>:- संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन का भुगतान करने के लिए मोबाइल पर भुगतान लिंक प्राप्त होगा| (<b>Ask to student </b>:- to pay Revised/Duplicate Marksheet/Migration as received payment link on mobile.)
                                                </span> 
                                                <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                                    <a href="{{ route('marksheet_revsied_duplicate_registration_fee',Crypt::encrypt(@$mmr_data->enrollment)) }}" style="">
														 संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन का भुगतान करने के लिए कृपया यहां क्लिक करें।।(Please click here to pay Revised/Duplicate Marksheet/Migration.)
                                                    <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment</span></a>
                                                </div>
                                            @endif
                                        
										<?php  } else{ ?>
										<?php  }  ?>
                                            
								<!--<div class="col x25 m12 s12"> -->
								<div class="">
								@foreach($master as $secotionFld => $values) 
                                    
									@php if($values['data'] != null){   @endphp 
                                        <div class="card">
                                            <div class="card-content invoice-print-area"> 
                                                @php echo $values['seciontLabel'];  
                                              
                                                @endphp 
                                                <!--<div class="divider mb-3 mt-3"></div>-->
                                                <div class="row">
                                                </br>
                                                    <div class="col m12 s12">
                                                        <table><tr> 
                                                            @php $counter = 0; @endphp 
                                                            @php if($values['data'] != null){
                                                                
                                                                @endphp 
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
								@if(@$mmr_data && !empty(@$mmr_data->total_fees))
									<div class="card">
										<div class="card-content invoice-print-area"> 
										<span style="font-weight: bold;color:#000000;">Student Fees Details</span>
												 
											<div class="row">
												</br>
												<div class="col m12">
													<table>
														<tr>
														  <td width="20%" class="callout callout-warning" style="font-size:16px;color:blue;">Total Fees
														  </td>
														  <td width="5%"> @php echo " : "; @endphp </td>
														  <td width="20%" style="font-size:16px;color:blue;">{{@$mmr_data->total_fees}}</td>			
														</tr>
													</table>
												</div>
											</div>	
										</div>
									</div>
								@endif
								<!--@if(@$revised_correction_data && @count($revised_correction_data) != 0)
                                <div class="card">
									<div class="card-content invoice-print-area"> 
											@php echo "Correction Value Details";  
                                            @endphp 
										<div class="row">
											</br>
											<div class="col m12">
											@foreach(@$revised_correction_data as $revised_data)
											
												<table>
													<tr>
                                                      <td width="20%">{{@$revised_data->correction_field}}
													  </td>
													  <td width="5%"> @php echo " : "; @endphp </td>
													  <td width="20%">{{@$revised_data->incorrect_value}}</td>			
													</tr>
												</table>
											@endforeach
											</div>
										</div>	
                                    </div>
                                </div>
								@endif	-->							
                                
									@php $documentErrors = false; @endphp
                                    @if(!$documentErrors) 
                                        <div class="card">
                                                @php $fld='locksumbitted'; @endphp 
                                                    @if(empty(@$mmr_data->$fld))
                                            <div class="card-content invoice-print-area">
                                                <div class="row">
                                                    <div class="col m12 s12">
                                                    @php $lbl=' घोषणा (Declaration)'; $fld='locksumbitted'; @endphp 
													@php $lbl1='Declaration'; $fld1='Declaration'; @endphp 
                                                        {{ Form::open(['url'=>url()->current(), 'id' =>'correction_preview']) }} 
                                                        {!! Form::token() !!} 
                                                        {!! method_field('PUT') !!} 
															<?php if(@$lockandsubmittedallowornot){ ?>
                                                          
															
															
															<p class="mb-1">
                                                                <label>
                                                                    
                                                                    {{ Form::checkbox($fld, null) }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/corr_preview_details.js') !!}"></script> 
@endsection 
