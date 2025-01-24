@extends('layouts.default') 
@section('content') 
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        <div class="card"> @include('elements.supplementary_top_navigation') </div>
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
                                
                            <?php } 
                            
                             
                            ?>
                            <?php if(!empty($lblForPaymentText)){ ?>
                                <span style="color:red;font-size:18px;">
                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/><?php echo $lblForPaymentText; ?>
                                </span>
                            <?php } ?>
                            <?php  
                            if(!empty(@$masterrecord->locksubmitted_date) &&  !empty($masterrecord->locksubmitted_date) && $masterrecord->fee_paid_amount <= 0){ ?>
                               <?php $lblPDFLink = "Download PDF"; ?>
                            <?php  } else { ?>
                                <?php $lblPDFLink = "Download Payment PDF"; ?>
                            <?php }?> 

                            @if(!empty(@$masterrecord->locksubmitted_date) && !empty($masterrecord->locksubmitted_date) )
                                <h4> 
                                    <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('supp_generate_student_pdf',Crypt::encrypt(@$masterrecord->student_id)) }}">
                                        <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
                                        <?php echo $lblPDFLink; ?>
                                    </a> 
                                </h4>
                            @endif
                            
                            <?php
                            if(!empty($masterrecord->locksubmitted_date) && $masterrecord->locksumbitted == 1){
                              
                                ?>
                                 
                                @if($masterrecord->category_a != 7)
                                    @if($application_fee > 0  && $masterrecord->fee_paid_amount <= 0 )
                                        <span style="color: red;font-size:14px;">
                                            <b>छात्र से </b>:- मोबाइल पर प्राप्त भुगतान लिंक के रूप में आवेदन पत्र शुल्क का भुगतान करने के लिए कहें| (<b>Ask to student </b>:- to pay application form fee as received payment link on mobile.)
                                        </span> 
                                        <div class="col-md-12 callout callout-warning" style="font-size:20px;"> 
                                            <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
                                            <a href="{{ route('supp_registration_fee',Crypt::encrypt(@$masterrecord->enrollment)) }}" style="">
                                                पूरक फॉर्म शुल्क का भुगतान करने के लिए कृपया यहां क्लिक करें। ।(Please click here to pay the supplementary form fee.)
                                            <i class="fa fa-tick"></i> <span class="btn btn-danger">Make Payment</span></a>
                                        </div>
                                    @endif
                                @endif
                                <?php  }else{ ?>
                            <?php  }  ?>
 

 
								 
							<div class="">
								@foreach($master as $secotionFld => $values) 
                                    @if( $secotionFld == "compulsorySubjectDetails" || $secotionFld == "additionalSubjectDetails")
                                        @php 
                                            continue;
                                        @endphp
                                    @endif
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

<div class="card">
                <div class="card-content invoice-print-area">
                    <div class="row">
                        <div class="col m12 s12"> 

                            <div><span style=""></span></div>
                            <div><span style=""><span class='customStrong'>&nbsp;अनिवार्य विषय(Compulsory Subject)</span></span></div>
                            <table border="1" style="width:100%;">
                                <tbody>
                                    <!--<tr>
                                        <td colspan="5"><span style="font-size:10px;"><span class='customStrong'>Compulsory Subject</span></span></td>
                                    </tr>-->
                                    <tr>
                                        @if(!empty($result))
                                        @foreach($result as $key => $values) 
                                        <td><span class='customStrong'><span style="">Subject{{ @$key + 1 }}</span></span></td>
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
                                                    {{@$values['final_result'] === "p" ? "(Pass)" : "" }}
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
                          

            <div class="card">
                <div class="card-content invoice-print-area">
                    <div class="row">
                        <div class="col m12 s12"> 
                            @if(@$mastersuppcount > 0)
                            <div class=""><span style=""><span class='customStrong'>&nbsp;अतिरिक्त विषय(Additional Subject)</span></span></div>
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
                             
                                
                          




                <div class="card">
                    <div class="card-content invoice-print-area">
                        <div class="row">
                            <div class="col m12 s12">
							
                                {{-- @dd($suppLateFeeDetails); --}}
                            <table class="table table-striped">
                                <tr>
                                    <th>Important Instructions</th>
                                    <th>महत्वपूर्ण निर्देश</th>
                                </tr>
                                <!--<tr><td>If you will not deposit the fee within 3 working days of filling the form, the data will be automatically deleted</td>
                                    <td>आप  फॉर्म  भरने  के ३ दिन के भीतर शुल्क  जमा करवाये अन्यथा डाटा स्वचालित रूप से हटा दिये जाएंगे|</td></tr>
                                    <tr><td>You can take print out of your filled application form after login with registered user credential at any time.</td>
                                    <td>आप किसी भी समय पंजीकृत लॉग इन और पासवर्ड के क्रेडेंशियल के  साथ लॉग इन  कर सकते हैं और अपने भरे हुए  आवेदन फॉर्म का प्रिंट आउट ले सकते हैं |</td></tr>
                                    <tr><td>After submission and payment of applicable fee, the student would print the three copies of filled form and submit to AI Center with requisite document.</td>
                                    <td>फॉर्म  प्रस्तुत करने और लागू शुल्क के भुगतान के बाद छात्र भरे हुए फॉर्म कि तीन प्रतियाँ  प्रिंट कर अपेक्षित दस्तावेज के साथ सन्दर्भ केंद्र पर जमा करवाए |</td></tr>-->
                                <tr>
                                    <td>The fees will be deposited in to the account of RSOS in 3 satges.<br>
                                        a)Stage 1 -(Without late fees)Within 3 working days of last date 26
                                        July,2018.<br>
                                        b)Stage 2 -(With 50/- late fees)Within 3 working days of last date 31 July,
                                        2018.<br>
                                        c)Stage 3 - (With 500/- late fees)Within 3 working days of last date 5 August
                                        2018.<br>
                                        - The data will be automatically deleted if the office does not receive the fees
                                        upto the given date.
                                    </td>
                                    <td>आवेदन पत्र भरने का शुल्क सन्दर्भ केन्द्र प्रभारी 3 चरणों में RSOS कार्यालय के
                                        खाते में जमा करायेगे। <br>
                                        (a) प्रथम चरण (साधारण शुल्क) 26 July 2018 से 3 कार्य दिवस तक ।<br>
                                        (b) द्वितीय चरण (50रू. विलम्ब शुल्क) 31 July 2018 से 3 कार्य दिवस तक।<br>
                                        (c) तृतीय चरण (500रू. असाधारण शुल्क ) 5 August 2018 से 3 कार्य दिवस तक । <br>
                                        -निर्धारित तिथि तक शुल्क कार्यालय को प्राप्त न होने पर डाटा स्वचालित रूप से हटा
                                        दिया जायेगा।
                                    </td>
                                </tr>
                                <tr>
                                    <td>The Incharge of A.I center can take the print out of filled application form
                                        after login with registered user credential at any time.</td>
                                    <td>सन्दर्भ केन्द्र प्रभारी किसी भी समय पंजीकृत लॉग इन और पासवर्ड क्रेडेंशियल के साथ
                                        लॉग इन कर, स्वयं (सन्दर्भ केन्द्र प्रभारी) द्वारा भरे हुए आवेदन फॅार्म का प्रिंट
                                        आउट ले सकते हैं।</td>
                                </tr>
                                <tr>
                                    <td>The AI Center Incharge will forward all the forms (with the photo copy of
                                        previous exam) to RSOS office after verifing them within 5 days and will keep
                                        one copy of every filled up application form for office record.</td>
                                    <td>सन्दर्भ केन्द्र प्रभारी अन्तिम तिथि पश्चात् 5 दिवस के भीतर आवेदन -पत्र (पूर्व
                                        परीक्षा की अंक तालिका की प्रति सहित) सत्यापित कर RSOS कार्यालय को प्रेषित करेंगे
                                        तथा आवेदन पत्रों की एक प्रति सन्दर्भ केन्द्र प्रभारी स्वयं के रिकार्ड हेतु
                                        संधारित करेगें।
                                    </td>
                                </tr>
                            </table><br>   
                                @php 
                                  
                                @endphp
                            @if(!$documentErrors) 
                                                @php $fld='locksumbitted';   @endphp 
                                                    @if(empty(@$masterrecord->$fld))
                                                 @php $lbl=''; $fld='locksumbitted'; @endphp 
                                                        {{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }} 
                                                        {!! Form::token() !!} 
                                                        {!! method_field('PUT') !!}  
                                                            <p class="mb-1">
                                                                <label>
																{{ Form::checkbox($fld, null) }}
                                                                    <span>@php echo Config::get('global.supp_undertaking_msg'); @endphp
                                                                    </span>
                                                                </label><br><br> 
								
                                                                @include('elements.field_error')
                                                            </p> 
                                                        <div class="col m7 s12 mb-3">
                                                            <button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Lock & Submit </button>
                                                        </div> 
                                                        {{ Form::close() }}
														@endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                    <div class="card">
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
	<script src="{!! asset('public/app-assets/js/bladejs/supplementary/supp_preview_details.js') !!}"></script> 
@endsection 