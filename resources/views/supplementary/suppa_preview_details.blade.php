@extends('layouts.default') 
@section('content') 
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        
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
                            // $lblCashFeePaymentLbl = '<span style="color: red; font-size:20px;">Notes:- अग्रेषण शुल्क रु. 5/- और ऑनलाइन सेवा शुल्क रु. 25/- एआई सेंटर को ऑफलाइन भुगतान करना होगा( Forwarding Fees of Rs. 5/- and Online Service Fees of Rs. 25/- to be paid offline to the AI center).</span></br></br>'; 
                            if(empty(@$masterrecord->locksubmitted_date) && @$masterrecord->category_a != 7 ){
                                $lblForPaymentText = "कृपया फॉर्म को लॉक और सबमिट करें और <b> लॉक और सबमिट करने के बाद आवेदन फॉर्म शुल्क का भुगतान ऑनलाइन करें</b>।(Please lock and submit form and after lock and submit should <b>online pay the application form fees</b>.)";
                            ?> 
                            <?php }else if(empty(@$masterrecord->locksubmitted_date) && @$masterrecord->category_a == 7 ){
                            
                                 $lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें और आप विशेष श्रेणी(". $categorya[@$masterrecord->category_a] ."  ) में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category (". $categorya[@$masterrecord->category_a] ."  ) so <b>No need to pay fees</b>.)";
                            ?>
                                
                            <?php } 
                            $lblForPaymentText = null;
                            ?>
                            <?php if(!empty($lblCashFeePaymentLbl)){ ?>
                                <span style="color:red;font-size:18px;">
                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblCashFeePaymentLbl; ?>
                                </span>
                            <?php } ?>
                            
                            <?php if(!empty($lblForPaymentText)){ ?>
                                <span style="color:red;font-size:18px;">
                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblForPaymentText; ?>
                                </span>
                            <?php } ?>
							
                            <?php  
                            if(!empty(@@$masterrecord->locksubmitted_date) &&  !empty(@$masterrecord->locksubmitted_date) && @$masterrecord->fee_paid_amount <= 0){ ?>
                               <?php $lblPDFLink = "Download PDF"; ?>
                            <?php  } else { ?>
                                <?php $lblPDFLink = "Download Payment PDF"; ?>
                            <?php }?> 

                            @if(!empty(@@$masterrecord->locksubmitted_date) && !empty(@$masterrecord->locksubmitted_date) )
                                <h4> 
                                    <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('supp_generate_admin_student_pdf',Crypt::encrypt(@@$masterrecord->student_id)) }}">
                                        <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                        <?php echo $lblPDFLink; ?>
                                    </a> 
                                </h4>
                            @endif 
                            
                            <?php
                            if(!empty(@$masterrecord->locksubmitted_date) && @$masterrecord->locksumbitted == 1){
							?>
                                @if(@$masterrecord->category_a != 7)
                                    @if($application_fee > 0  && @$masterrecord->fee_paid_amount <= 0 )
                                        <span style="color: red;font-size:14px;">
                                            <b>छात्र से </b>:- मोबाइल पर प्राप्त भुगतान लिंक के रूप में आवेदन पत्र शुल्क का भुगतान करने के लिए कहें| (<b>Ask to student </b>:- to pay application form fee as received payment link on mobile.)
                                        </span> 
                                        <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                            <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                            <a href="{{ route('supp_registration_fee',Crypt::encrypt(@@$masterrecord->enrollment)) }}" style="">
											पूरक फॉर्म शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें। (Please click here to pay the supplementary form fee.)
                                            <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment</span></a>
                                        </div>
                                    @endif
                                @endif
                                <?php  } ?>
 
							<div class="">
								@foreach($supp_master as $secotionFld => $values) 
                                    @if( $secotionFld == "compulsorySubjectDetails" || $secotionFld == "additionalSubjectDetails")
                                        @php 
                                            continue;
                                        @endphp
                                    @endif
									@php if($values['data'] != null){   @endphp 
									<div class="card">
                                        <div class="card-content invoice-print-area"> 
											@php echo '<p style="font-weight: 1000;">'.@$values['seciontLabel'].'</p>';  @endphp 
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
														<td width="20%"><p style="font-size: 16px;font-weight: bold;">@php echo $lbl['label']; @endphp</p></td>
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
				<div class="card">
                <div class="card-content invoice-print-area">
                    <div class="row">
                        <div class="col m12 s12"> 

                            <div><span style=""></span></div>
                            <div><span style=""><p style="font-weight: 1000;">&nbsp;अनिवार्य विषय ( Compulsory Subject )</p></div>
                            <table border="1" style="width:100%;">
                                <tbody>
                                    <!--<tr>
                                        <td colspan="5"><span style="font-size:10px;"><span class='customStrong'>Compulsory Subject</span></span></td>
                                    </tr>-->
                                    <tr>
									  
                                        @if(!empty($result))
                                        @foreach($result as $key => $values) 
                                        <td><p style="font-weight: 1000;">Subject{{ @$key + 1 }}</p></td>
                                        @endforeach
                                        @endif 
                                    </tr> 
                                    <tr>
                                        @if(!empty($result))
                                        @foreach($result as $key => $values) 
										<td>
                                            <span>
                                                {{ @$subject_list[$values['subject_id']] }}&nbsp;&nbsp;
                                                <span style="font-weight: bold;font-size: 16px;color: green;">
                                                    <?php
													if(isset($values['final_result']) && @$values['final_result']=="P") { 
														echo "(Pass)"; 
													}  ?>
                                                </span>
                                            </span>
                                        </td>
                                        @endforeach
                                        @endif

                                    </tr>
                                        
                                </tbody>
                            </table>
						</div>
					</div>
				</div>
			</div>
            
			
			@if(@$mastersuppcount > 0)
			<div class="card">
                <div class="card-content invoice-print-area">
                    <div class="row">
                        <div class="col m12 s12"> 
                            @if(@$mastersuppcount > 0)
                            <div class=""><p style="font-weight: 1000;">&nbsp;अतिरिक्त विषय ( Additional Subject)</p></div>
                            <table border="1" style="width:100%;">
                                <tbody>
                                    <!--<tr>
                                        <td colspan="5"><span class='customStrong'><span style="font-size:10px;">Additional Subject</span></span></td>
                                    </tr>-->
                                    
                                    <tr>
                                        @for($i=1;$i <= $mastersuppcount;$i++) 
                                        <td><span class='customStrong'><span style="">Subject{{ $i }}</span></span></td>
                                        @endfor
                                    </tr>
                                    
                                    <tr>
                                        @foreach(@$mastersupp as $key => $values)
                                        @if(@$values['is_additional_subject']==1)
                                        <td><span style="">{{ $subject_list[$values['subject_id']] }}</span></td>
                                        @endif
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table><br>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
			@endif                    
            </div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/supplementary/supp_preview_details.js') !!}"></script> 
@endsection 