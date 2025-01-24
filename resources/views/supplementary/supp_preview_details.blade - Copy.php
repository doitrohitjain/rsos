@extends('layouts.default') 
@section('content') 
@php 
    $role_id = @Session::get('role_id');
    $aicenter_id_role_id = Config::get('global.aicenter_id');
    $examination_department_role_id = Config::get('global.examination_department');
@endphp
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    @if($role_id == Config::get("global.developer_admin"))
                        <div class="col s12 m12 l12"><div class="card row">
                                <div class="card-content">
                                    @can('aicenter_verify_revert_action')
                                        <a data-url="{{ route('revartVerifyStatus',[Crypt::encrypt($suppData->id),$aicenter_id_role_id]) }}" class="btn btn-xs btn-success right mb-2 mr-1  aicenter_verify" style="">
                                            Reset Ai Center Verifyed
                                        </a>
                                    @endcan 
                                    @can('department_verify_revert_action')
                                        <a data-url="{{ route('revartVerifyStatus',[Crypt::encrypt($suppData->id),$examination_department_role_id]) }}" class="btn btn-xs btn-success right mb-2 mr-1 department_verify">
                                            Reset Department Verifyed
                                        </a>
                                    @endcan	
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col s12 m12 l12">
                        <div class="card">  @include('elements.supplementary_top_navigation') </div>
                    </div>
                    <div class="col s12 m12 l12" style="background-color: #4f505245;'">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{ $page_title; }}</h4>  
                            @php 
                                $fld='locksumbitted'; 
                            @endphp  
                            @if(!empty(@@$masterrecord->locksubmitted_date) &&  !empty(@$masterrecord->locksubmitted_date) && @$masterrecord->fee_paid_amount >= 0) 
								@if($masterrecord->is_eligible != 1)
								@php $verifingTxt = "सफल भुगतान के बाद आपके पूरक आवेदन पत्र को ऊपरी स्तरों द्वारा सत्यापित किया जाएगा।(After successful payment your supplementary application form should verify by the upper levels.)";@endphp
								<div style='color:blue;font-size:18px;'> 
								<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
								{{ $verifingTxt }} </div>
								@endif
							@endif
							<?php  
                            $lblForPaymentText = null;
                            // $lblCashFeePaymentLbl = '<span style="color: red; font-size:20px;">Notes:- अग्रेषण शुल्क रु. 5/- और ऑनलाइन सेवा शुल्क रु. 25/- एआई सेंटर को ऑफलाइन भुगतान करना होगा( Forwarding Fees of Rs. 5/- and Online Service Fees of Rs. 25/- to be paid offline to the AI center).</span></br></br>'; 
							
                            if(empty(@$masterrecord->locksubmitted_date) && @$masterrecord->category_a != 7 ){
                                $lblForPaymentText = "कृपया फ़ॉर्म लॉक और सबमिट करें। सबमिशन के बाद, 'Make Payment' पर क्लिक करके ऑनलाइन आवेदन शुल्क भरे।</b><br>(Please lock and submit the form. After submission, you can pay the application fees online by clicking on </br><b>'Make Payment'</b>.)";
                            ?> 
                            <?php }else if(empty(@$masterrecord->locksubmitted_date) && @$masterrecord->category_a == 7 ){
                            
                                 $lblForPaymentText = "कृपया फॉर्म को लॉक करके सबमिट करें , आप विशेष श्रेणी(". $categorya[@$masterrecord->category_a] ."  ) में आ गए हैं इसलिए <b>शुल्क का भुगतान करने की कोई आवश्यकता नहीं है</b>।(Please lock and submit form and you are come in special category (". $categorya[@$masterrecord->category_a] ."  ) so <b>No need to pay fees</b>.)";
                            ?>
                                
                            <?php } 
                            
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
                                    <a class="btn btn-success" style ="margin-left: 60%;" href="{{ route('supp_generate_student_pdf',Crypt::encrypt(@@$masterrecord->student_id)) }}">
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
                                            <b>छात्र </b>:- मोबाइल पर प्राप्त भुगतान लिंक के रूप में आवेदन पत्र शुल्क का भुगतान करने के लिए कहें| (<b>Ask to student </b>:- to pay application form fee as received payment link on mobile.)
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

                                <div class="card">
                                    <div class="card-content row">
                                        <div class="col s6 m6 l6">
											अंतिम सत्यापन स्थिति(Final Verification Status) : 
                                                @if(@$master->supplementary->is_department_verify == 2 && @$master->supplementary->is_aicenter_verify == 2)
                                                    <span style="font-size:18px;font-weight:bold;color:green;"> Approved</span>
                                                @else
                                                    <span style="font-size:18px;font-weight:bold;color:red !important;"> Not Approved</span>
                                                @endif  
                                        </div>
                                        <div class="col s6 m6 l6">
                                            
                                        @include('elements.supplementary_action_approve_reject')
                                           

                                        </div>  
                                    </div>
                                </div>


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
														<td width="20%"><p style="font-size: 16px;font-weight: bold;">@php echo @$lbl['label']; @endphp</p></td>
														<td width="5%"> @php echo " : "; @endphp </td>
														<td width="20%"> @php echo @$lbl['value']; @endphp </td>
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
            
            @if($role_id != Config::get("global.student"))
                <div class="card">
                    <div class="card-content invoice-print-area">
                        <p style="font-weight: 1000;">अंतिम सत्यापन स्थिति: लंबित/स्वीकृत/अस्वीकृत ( Final Verfication Status : Pending/Approved/Rejected )</p>
                        <div class="row">
                            <div class="col m12 s12 scroll">
                                <table class="table table-striped">
                                    <tr>
                                        <td>एआई केंद्र द्वारा सत्यापित है  ?(Is Verified By Aicenter)</td>
                                        <td><p>{{ @$supp_verfication_status[@$masterrecord->is_aicenter_verify] }}</td>
                                    </tr>
                                    @if($role_id == Config::get("global.examination_department"))
                                       
										<tr>
                                            <td>विभाग द्वारा सत्यापित है  ?(Is Verified By Department)</td>
                                            <td>{{ @$supp_verfication_status[@$masterrecord->is_department_verify] }}</td>
                                        </tr>
                                    @endif 
                                </table>
                            </div>
                        </div> 
						
                        @if(@$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status == 3 && $suppData->is_aicenter_verify == 3)
                            <p style="font-weight: 1000;"> एआई केंद्र द्वारा टिप्पणियाँ( Remarks By AI Center)</p>
                            <div class="row">
                                <div class="col m12 s12 scroll">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><p>{{ @$suppVerifcationData->aicenter_remark  }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif  
                    </div>
                </div>
             
                @if($role_id == Config::get("global.examination_department"))
                    @if(@$suppVerifcationData->department_status && @$suppVerifcationData->department_status && @$suppVerifcationData->department_status == 3  && $suppData->is_department_verify == 3)
                        <p style="font-weight: 1000;"> विभाग द्वारा टिप्पणियाँ( Remarks By Department)</p>
                        <div class="row">
                            <div class="col m12 s12 scroll">
                                <table class="table table-striped">
                                    <tr>
                                        <td><p>{{ @$suppVerifcationData->department_remark  }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif  
                @endif
			@else
				
				@if(@$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status == 3 && $suppData->is_aicenter_verify == 3)
					<p style="font-weight: 1000;"> एआई केंद्र द्वारा टिप्पणियाँ( Remarks By AI Center)</p>
					<div class="row">
						<div class="col m12 s12 scroll">
							<table class="table table-striped">
								<tr>
									<td><p>{{ @$suppVerifcationData->aicenter_remark  }}</td>
								</tr>
							</table>
						</div>
					</div>
				@endif   
			 
				@if(@$suppVerifcationData->department_status && @$suppVerifcationData->department_status && @$suppVerifcationData->department_status == 3  && $suppData->is_department_verify == 3)
					<p style="font-weight: 1000;"> विभाग द्वारा टिप्पणियाँ( Remarks By Department)</p>
					<div class="row">
						<div class="col m12 s12 scroll">
							<table class="table table-striped">
								<tr>
									<td><p>{{ @$suppVerifcationData->department_remark  }}</td>
								</tr>
							</table>
						</div>
					</div>
				@endif  
                
				
            @endif

            
		
		
        
                <div class="card">
                    <div class="card-content invoice-print-area">
                        <div class="row">
                            <div class="col m12 s12">
							
                               
                            <table class="table table-striped">
                                <tr>
                                    <th><p style="font-weight: 1000;">Important Instructions</p></th>
                                    <th><p style="font-weight: 1000;">महत्वपूर्ण निर्देश</p></th>
                                </tr>
                                <!--<tr><td>If you will not deposit the fee within 3 working days of filling the form, the data will be automatically deleted</td>
                                    <td>आप  फॉर्म  भरने  के ३ दिन के भीतर शुल्क  जमा करवाये अन्यथा डाटा स्वचालित रूप से हटा दिये जाएंगे|</td></tr>
                                    <tr><td>You can take print out of your filled application form after login with registered user credential at any time.</td>
                                    <td>आप किसी भी समय पंजीकृत लॉग इन और पासवर्ड के क्रेडेंशियल के  साथ लॉग इन  कर सकते हैं और अपने भरे हुए  आवेदन फॉर्म का प्रिंट आउट ले सकते हैं |</td></tr>
                                    <tr><td>After submission and payment of applicable fee, the student would print the three copies of filled form and submit to AI Center with requisite document.</td>
                                    <td>फॉर्म  प्रस्तुत करने और लागू शुल्क के भुगतान के बाद छात्र भरे हुए फॉर्म कि तीन प्रतियाँ  प्रिंट कर अपेक्षित दस्तावेज के साथ सन्दर्भ केंद्र पर जमा करवाए |</td></tr>-->
                                <tr>
                                    <td>The fees will be deposited in 3 stages.<br>
                                        (a) Stage 1 : Without late fees from 15th March, 2024 to 29th March, 2024.<br>
                                        (b) Stage 2 : With 50/- per subject late fees from 30th March, 2024 to 1st April, 2024.<br>
                                        (c) Stage 3 : With 500/- late fees from 2nd April, 2024 to 3rd April, 2024.<br>
                                    </td>
                                    <td>आवेदन पत्र भरने का शुल्क  3 चरणों में जमा होगा <br>
                                        (a) प्रथम चरण : साधारण शुल्क दिनांक 15th मार्च, 2024 से 29th मार्च, 2024 तक।<br>
                                        (b) द्वितीय चरण : 50रू. प्रति विषय विलम्ब शुल्क  दिनांक 30th मार्च, 2024 से 1st अप्रैल, 2024 तक।<br>
                                        (c) तृतीय चरण : 500रू. असाधारण विलम्ब शुल्क  दिनांक 2nd अप्रैल, 2024  से 3rd अप्रैल, 2024 तक। <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td>The Incharge of A.I center can take the print out of filled application form
                                        after login with registered user credential at any time.</td>
                                    <td>सन्दर्भ केन्द्र प्रभारी किसी भी समय पंजीकृत लॉग इन और पासवर्ड क्रेडेंशियल के साथ
										लॉग इन कर, अभ्यर्थी द्वारा भरे हुए आवेदन फॅार्म का प्रिंट
										आउट ले सकते हैं।
									</td>
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
                                                    @if(empty(@@$masterrecord->$fld))
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