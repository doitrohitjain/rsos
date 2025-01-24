@extends('layouts.default')
@section('content')
<div id="main">
     <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
				   @if(Auth::guard('student')->user()->is_verified == 2)
					   <div class="card">
                        <span style="color:red;font-size:20px;">
                            @php 
                                echo Config::get('global.student_doc_rejected_notification');
                            @endphp
                        </span>
                    </div> 
                   @endif
                    <div class="card">
                       
                   <div class="card-content">
                    <div class="row">
					<h3><center style='gradient-45deg-deep-orange-orange;'><b>Welcome,{{Auth::guard('student')->user()->name;}}</b></center></h3>
					
					<?php 
                            $lists = array();
							if(@$allowHllTicketIps == true){
								if(@$isAllowToShowAdmitCardDownloadForStudent){
									$lists[] = array('lbl_text' =>'राजस्थान स्टेट ओपन स्कूल द्वारा परीक्षा सत्र मार्च-मई 2023-24 के प्रवेश पत्र जारी कर दिए गए हैं। प्रवेश पत्र डाउनलोड करने के लिए कृपया "Admit Card" बटन पर क्लिक करें।(Rajasthan State Open School has released the admit cards for the exam session March-May 2023-24. Please click on the "Admit Card" button to download the admit card', 'title' => 'Click here to download your hall ticket.', 'btnText' => 'Admit Card.','noteText'=>"प्रवेश पत्र(Admit Card) को संदर्भ केन्द्र से प्रमाणित करने की आवश्यकता नहीं है।", 'is_new' => true, 'route' => route('downloadAdmitCard',encrypt(Auth::guard('student')->user()->enrollment)));
								}
							}  
							
							
							
						
								if(@$studentallowornot && $studentallowornot == true && (Auth::guard('student')->user()->student_change_requests == null)){
									$path = route('generate_student_pdf',Crypt::encrypt(@$student_id));	
									$lists[] = array('lbl_text' =>'आपका आवेदन पत्र अंततः उपयुक्त प्राधिकारियों द्वारा अनुमोदित कर दिया गया है। कृपया अपना आवेदन पत्र पुनः प्राप्त करने के लिए "डाउनलोड" बटन पर क्लिक करें।(Your application form has been finally approved by the appropriate authorities. Please click the "Download" button to retrieve your application form.)', 'btnText' => 'Download Application Form', 'is_new' => true, 'route' => $path);
							    }elseif((Auth::guard('student')->user()->student_change_requests == null && @ Auth::guard('student')->user()->update_change_requests_challan_tid == null)){
									$path = route('preview_details',Crypt::encrypt(@$student_id));	
									$lists[] = array('lbl_text' =>'अपना आवेदन पत्र पूरा करें क्योंकि आपका आवेदन अधूरा है। ( Complete your application form as your previous submission was incomplete.)', 'btnText' => 'Fill Latest Applicaton', 'is_new' => true, 'route' => $path);
                                  }elseif(!empty(@ Auth::guard('student')->user()->update_change_requests_challan_tid)){
									$path = route('generate_student_pdf',Crypt::encrypt(@$student_id));	
									$lists[] = array('lbl_text' =>'आपका आवेदन पत्र अंततः उपयुक्त प्राधिकारियों द्वारा अनुमोदित कर दिया गया है। कृपया अपना आवेदन पत्र पुनः प्राप्त करने के लिए "डाउनलोड" बटन पर क्लिक करें।(Your application form has been finally approved by the appropriate authorities. Please click the "Download" button to retrieve your application form.)', 'btnText' => 'Download Application Form', 'is_new' => true, 'route' => $path);
                               }  
								/* old
								if(@$studentallowornot && $studentallowornot == true){
									$path = route('generate_student_pdf',Crypt::encrypt(@$student_id));	
									$lists[] = array('lbl_text' =>'आपका आवेदन पत्र अंततः उपयुक्त प्राधिकारियों द्वारा अनुमोदित कर दिया गया है। कृपया अपना आवेदन पत्र पुनः प्राप्त करने के लिए "डाउनलोड" बटन पर क्लिक करें।(Your application form has been finally approved by the appropriate authorities. Please click the "Download" button to retrieve your application form.)', 'btnText' => 'Download Application Form', 'is_new' => true, 'route' => $path);
							    }else{
									$path = route('preview_details',Crypt::encrypt(@$student_id));	
									$lists[] = array('lbl_text' =>'अपना आवेदन पत्र पूरा करें क्योंकि आपका आवेदन अधूरा है। ( Complete your application form as your previous submission was incomplete.)', 'btnText' => 'Fill Latest Applicaton', 'is_new' => true, 'route' => $path);
								} 
								old */
							
							/* $path = route('preview_details',Crypt::encrypt(@$student_id));	
							$lists[] = array('lbl_text' =>'अपना आवेदन पत्र पूरा करें क्योंकि आपका आवेदन अधूरा है। ( Complete your application form as your previous submission was incomplete.)', 'btnText' => 'Fill Applicaton', 'is_new' => true, 'route' => $path); */
                            if(@$revalDetails->rte_reval_notify_date && $revalDetails->rte_reval_notify_date >= now()){
                                $lblText = "Please visit the RSOS office to obtain the RTI copy dated  ".date("d M,Y",strtotime($revalDetails->rte_reval_notify_date)) . ".";

                                $lists[] = array('lbl_text' =>$lblText, 'title' => '', 'btnText' => '', 'is_new' => true, 'route' => route('landing'));
                            }
							
						 	//@dd($getCurrentFreshVerifydata);
                            if(@$getcurrentsuppverifydata->is_per_rejected && $getcurrentsuppverifydata->is_per_rejected == 1){
								$lists[] = array('lbl_text' =>'आपका पूरक आवेदन पत्र विभाग द्वारा स्थायी रूप से अस्वीकार कर दिया गया है।(Your Supplementary Application Form permanently rejected by department.)', 'title' => 'Rejected.', 'btnText' => 'Rejected',  'customCls' => 'btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text', 'is_new' => true, 'route' => 'javascript:void(none);');
							}else{
								if(@$getcurrentsuppverifydata->is_aicenter_verify == 3 || @$getcurrentsuppverifydata->is_department_verify == 3){
									$lists[] = array('lbl_text' =>'कृपया सत्यापन स्तर पर अंकित अपनी लंबित जानकारी पूरी करें।(Please complete your pending information as marked at verification level.)', 'title' => 'Click here to complete the pending details.', 'btnText' => 'Submit Clarification',  'customCls' => 'btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text', 'is_new' => true, 'route' => route('supp_verfication_document'));
								} 
									
							}
                            
							/* Verification Start */
							//@dd(@$isFinallyRejectedByDept);
								if(@$dateIsOpen && $dateIsOpen == true && @$isFinallyRejectedByDept){
									$lists[] = array('lbl_text' =>'संबंधित विभाग द्वारा पुनः स्पष्टीकरण के बाद आपका आवेदन पत्र स्थायी रूप से अस्वीकार कर दिया गया है।(Your application form, following re-clarification by the relevant department, has been permanently rejected.)', 'title' => 'Click here to permanently rejected.', 'btnText' => 'Permanently Rejected','customCls' => 'btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text','is_new' => true, 'route' => 'javascript:void(none);');
								}else{
									// dd($getCurrentFreshVerifydata->is_doc_rejected);
									//echo $dateIsOpen.'_'.$getCurrentFreshVerifydata;die;
									
									if(@$dateIsOpen && $dateIsOpen == true && $getCurrentFreshVerifydata !== null){
										if((@$getCurrentFreshVerifydata->is_doc_rejected == 3 || @$getCurrentFreshVerifydata->department_status == 3 ) && $getCurrentFreshVerifydata->is_doc_rejected == 1 && @$studentDocumentVerificaitonData->is_eligible_for_verify != 1){
											$lists[] = array('lbl_text' =>'कृपया अपने आवेदन पत्र के सत्यापन स्तर पर अंकित अपनी लंबित जानकारी को पूरा करें।(Please complete your pending information as marked at verification level for your application form.)', 'title' => 'Click here to complete the pending details.', 'btnText' => 'Submit Clarification', 'customCls' => 'btn white-text secondary-content waves-yellow gradient-45deg-deep-orange-orange white-text', 'is_new' => true, 'route' => route('rejected_document_details',Crypt::encrypt(@$student_id)));
										}elseif( (@$getCurrentFreshVerifydata->department_status == 2 || @$getCurrentFreshVerifydata->ao_status == 2)){
											$path = route('generate_student_pdf',Crypt::encrypt(@$student_id));	
											$lists[] = array('lbl_text' =>'आपका आवेदन पत्र अंततः उपयुक्त प्राधिकारियों द्वारा अनुमोदित कर दिया गया है। कृपया अपना आवेदन पत्र पुनः प्राप्त करने के लिए "डाउनलोड" बटन पर क्लिक करें।(Your application form has been finally approved by the appropriate authorities. Please click the "Download" button to retrieve your application form.)', 'btnText' => 'Download Application Form', 'is_new' => true, 'route' => $path); 	 
										}else if(@$getCurrentFreshVerifydata->verifier_status != 9  && @$getCurrentFreshVerifydata->ao_status == 3 && $getCurrentFreshVerifydata->is_doc_rejected == 1 && $getCurrentFreshVerifydata->stage == 3){
											$lists[] = array('lbl_text' =>'आपके आवेदन की सत्यापन प्रक्रिया के दौरान अपलोड किए गए दस्तावेजों में विसंगति पाई गई। प्रथम अपील के लिए, कृपया आवश्यक दस्तावेज़ दोबारा अपलोड करें।(During the verification process of your application, a discrepancy was found in the uploaded documents. Please upload the required document again for first appeal.)', 'title' => 'Your application details have been marked as rejected due to reasons other than the uploaded documents.', 'btnText' => 'Upload Document', 'customCls' => 'btn white-text secondary-content waves-yellow gradient-45deg-deep-orange-orange white-text', 'is_new' => true, 
											'route' => route('rejected_document_details',Crypt::encrypt(@$student_id)));
											
										}else if(@$getCurrentFreshVerifydata->department_status != 10 && @$getCurrentFreshVerifydata->ao_status == 3 && $getCurrentFreshVerifydata->is_doc_rejected == 1 && $getCurrentFreshVerifydata->stage == 4){
											$lists[] = array('lbl_text' =>'आपके आवेदन की सत्यापन प्रक्रिया के दौरान अपलोड किए गए दस्तावेजों में विसंगति पाई गई। द्वितीय अपील के लिए, कृपया आवश्यक दस्तावेज़ दोबारा अपलोड करें।(During the verification process of your application, a discrepancy was found in the uploaded documents. Please upload the required document again for second appeal.)', 'title' => 'Your application details have been marked as rejected due to reasons other than the uploaded documents.', 'btnText' => 'Upload Document', 'customCls' => 'btn white-text secondary-content waves-yellow gradient-45deg-deep-orange-orange white-text', 'is_new' => true, 
											'route' => route('rejected_document_details',Crypt::encrypt(@$student_id)));
											
										}else{ 
											if(@$getCurrentFreshVerifydata->department_status == 4 && @$studentDocumentVerificaitonData->is_eligible_for_verify != 1) { 
												$lists[] = array('lbl_text' =>'कृपया अपना स्पष्टीकरण प्रस्तुत करने के लिए अपना लंबित भुगतान पूरा करें।।(Please complete your pending payment to submit your clarification.)', 'title' => 'Click here to complete the pending details.', 'btnText' => 'Submit Payment Clarification', 'is_new' => true, 'route' => route('rejected_document_details',Crypt::encrypt(@$student_id)));
											// }elseif(@$studentDocumentVerificaitonData->is_eligible_for_verify != 1 && (@$getCurrentFreshVerifydata->department_status == 2 || @$getCurrentFreshVerifydata->ao_status == 2)){
										}elseif( (@$getCurrentFreshVerifydata->department_status == 2 || @$getCurrentFreshVerifydata->ao_status == 2)){
													$path = route('generate_student_pdf',Crypt::encrypt(@$student_id));	
													$lists[] = array('lbl_text' =>'आपका आवेदन पत्र अंततः उपयुक्त प्राधिकारियों द्वारा अनुमोदित कर दिया गया है। कृपया अपना आवेदन पत्र पुनः प्राप्त करने के लिए "डाउनलोड" बटन पर क्लिक करें।(Your application form has been finally approved by the appropriate authorities. Please click the "Download" button to retrieve your application form.)', 'btnText' => 'Download Application Form', 'is_new' => true, 'route' => $path);
														 
											}else if(@$studentDocumentVerificaitonData->is_eligible_for_verify == 1){
													$lists[] = array('lbl_text' =>'कृपया अपने आवेदन पत्र के सत्यापन के लिए उपयुक्त विभाग को अपना स्पष्टीकरण प्रस्तुत करने तक प्रतीक्षा करें।(Please wait while your clarification submitted to the appropriate department for verification your application form.)', 'title' => 'Click here already submitted for clarificaiton', 'btnText' => 'Already Submitted', 'is_new' => true, 'route' => 'javascript:void(none);');
												 
											}
										} 
									}
									
								}
								
							/* Verification End */
						    
							if((@$isAllowForSuppApplicaitonForm && $isAllowForSuppApplicaitonForm == true && @$getsupplementarychangerequertstudentdatas->supp_student_change_requests == null) || (@$suppchangerequeststudent->supp_student_update_application == null)){
                                $lists[] = array('lbl_text' =>' पूरक परीक्षा आवेदन ' . $formApplyHindiTxt .'  के लिए "' . $formStatus .' Supplementary" बटन पर क्लिक करें।(Click on the "' . $formStatus .' Supplementary" button to ' . $formStatus .' for a supplementary exam application form.)', 'title' => 'Click here to ' . $formStatus .' for supplementary application.', 'btnText' => '' . $formStatus .' Supplementary', 'is_new' => true, 'route' => route('supp_subjects_details',Crypt::encrypt(@$student_id))); 
                            }
							
							if((@$isAllowForRevalApplicaitonForm == true && @$getsupplementarychangerequertstudentdatas->supp_student_change_requests == null) || (@$suppchangerequeststudent->supp_student_update_application == null)){
                                $btnLbl = " Apply ";
                                if(@$revalCompleteDetails->is_eligible){
                                    $btnLbl = " View ";
                                }
								$lists[] = array('lbl_text' =>' पुनर्मूल्यांकन आवेदन करने के लिए "' . $btnLbl . ' Reval" बटन पर क्लिक करें।(Click on the "' . $btnLbl . ' Reval" button to ' . $btnLbl . ' for ' . $btnLbl . ' reval application form.)', 'title' => 'Click here to ' . $btnLbl . ' for reval application.', 'btnText' => '' . $btnLbl . ' Reval', 'is_new' => true, 'route' => route('reval_subjects_details',Crypt::encrypt(@$student_id))); 
							}
							
							if(@$correctionAllowOrNot == true){
							$lists[] = array('lbl_text' =>'संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन के लिए आवेदन करें।(Apply for Revised/Duplicate Marksheet/Migration ', 'title' => 'Click here to Apply for Revised/Duplicate Marksheet/Migration .', 'btnText' => 'Marksheet/Migration','is_new' => true, 'route' => route('marksheetCorreaction',Crypt::encrypt(@$student_id)));
							}
							
                        ?> 
				            @if(@$allowchnagerequestAllowIps == true)
						    @if(@$checkchangerequestsAllowOrNotAllow == true)
						    @can('student_change_requests')
						    <?php
							 if(@$changerequeststreamgatdata->option_val == 3){
								if(Auth::guard('student')->user()->student_change_requests == 1 && Auth::guard('student')->user()->can('student_change_request_status')){	
								$lists[] = array('lbl_text' =>'आपके द्वारा आवेदन फॉर्म को अपडेट करने के लिए अनुरोध कर दिया गया है।(You have requested to update your application form).', 'is_new' => true,);	
							   }
							   elseif(Auth::guard('student')->user()->student_change_requests == 2 && Auth::guard('student')->user()->can('student_change_request_update_application_button')){	
								$path = route('student_change_requests_update_application',Crypt::encrypt(@$student_id));	
								$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट के लिए  "Update Application" बटन पर क्लिक करें(Click on "Update Application" button for update in your application form)', 'btnText' => 'Update Application', 'is_new' => true, 'route' => $path);
							   }
							    elseif($getstudentchangerequertdatas > 0 && Auth::guard('student')->user()->can('student_change_request_button')){
								$path = route('student_change_requests',Crypt::encrypt(@$student_id));
								$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट करने के लिए "Change Request" बटन पर क्लिक करें।(To update your application form , click on "Change Request" button)', 'btnText' => 'Change Request', 'is_new' => true, 'route' => $path);
							   }//elseif($getstudentchangerequertdatas > 0 && Auth::guard('student')->user()->ao_status == 2){
								//$path = route('student_change_requests',Crypt::encrypt(@$student_id));	
								//$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट करने के लिए "Change Request" बटन पर क्लिक करें।(To update your application form , click on "Change Request" button)', 'btnText' => 'Change Request', 'is_new' => true, 'route' => $path);
							   //} 
							 }else{
							  if(Auth::guard('student')->user()->student_change_requests == 1 && Auth::guard('student')->user()->exam_month == @$changerequeststreamgatdata->option_val && Auth::guard('student')->user()->can('student_change_request_status')){
								$lists[] = array('lbl_text' =>'आपके द्वारा आवेदन फॉर्म को अपडेट करने के लिए अनुरोध कर दिया गया है।(You have requested to update your application form).', 'is_new' => true,);
							   }
							   elseif(Auth::guard('student')->user()->student_change_requests == 2  && @$changerequeststudentsdataid->exam_month == @$changerequeststreamgatdata->option_val && Auth::guard('student')->user()->can('student_change_request_update_application_button')){	
								$path = route('student_change_requests_update_application',Crypt::encrypt(@$student_id));	
								$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट के लिए  "Update Application" बटन पर क्लिक करें(Click on "Update Application" button for update in your application form)', 'btnText' => 'Update Application', 'is_new' => true, 'route' => $path);
								
							   }
							    elseif($getstudentchangerequertdatas > 0 && Auth::guard('student')->user()->exam_month == $changerequeststreamgatdata->option_val && Auth::guard('student')->user()->can('student_change_request_button')){
								$path = route('student_change_requests',Crypt::encrypt(@$student_id));
								$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट करने के लिए "Change Request" बटन पर क्लिक करें।(To update your application form , click on "Change Request" button)', 'btnText' => 'Change Request', 'is_new' => true, 'route' => $path);
							   }
							   //elseif($getstudentchangerequertdatas > 0 && Auth::guard('student')->user()->ao_status == 2 &&Auth::guard('student')->user()->exam_month == $changerequeststreamgatdata->option_val){
								//$path = route('student_change_requests',Crypt::encrypt(@$student_id));	
								//$lists[] = array('lbl_text' =>'अपने आवेदन पत्र में अपडेट करने के लिए "Change Request" बटन पर क्लिक करें।(To update your application form , click on "Change Request" button)', 'btnText' => 'Change Request', 'is_new' => true, 'route' => $path);
							   //}	 
							 }
							   
							   ?>
							@endcan
							@endif
							@endif
							
							@if(@$chnagerequestsupplementariesallowIps == true)
						    @if(@$checkchangerequestsssupplementariesAllowOrNotAllow == true)
						    @can('supp_student_change_requests')
						    <?php
							   if(@$getsupplementarychangerequertstudentdatas->supp_student_change_requests == 1 && Auth::guard('student')->user()->can('supp_student_change_request_status')){	
								$lists[] = array('lbl_text' =>'आपके द्वारा  पूरक आवेदन फॉर्म को अपडेट करने के लिए अनुरोध कर दिया गया है।(You have requested to update your Supplementary form).', 'is_new' => true,);	
							   }
							   elseif(@$getsupplementarychangerequertstudentdatas->supp_student_change_requests == 2 && Auth::guard('student')->user()->can('supp_student_change_requests_update_applications') ){	
								$path = route('supp_student_change_requests_update_application',Crypt::encrypt(@$student_id));	
								$lists[] = array('lbl_text' =>'अपने पूरक आवेदन पत्र में अपडेट के लिए  " Update Supplementary From" बटन पर क्लिक करें(Click on "Update Supplementary From"  button to update in your Supplementary from)', 'btnText' => 'Update Supp. From', 'is_new' => true, 'route' => $path);
							   }
							    elseif(@$getsupplementarychangerequertdatas > 0 && Auth::guard('student')->user()->can('supp_student_change_request_button') && @$getsupplementarychangerequertstudentdatas->is_department_verify == 2){
								$path = route('student_supp_change_requests',Crypt::encrypt(@$student_id));	
								$lists[] = array('lbl_text' =>'अपने पूरक आवेदन पत्र में अपडेट करने के लिए "Supplementary Change Request" बटन पर क्लिक करें।(To update your Supplementary form ,plase  click on " Supplementary Change Request" button)', 'btnText' => 'Supp. Change Request', 'is_new' => true, 'route' => $path);
							   }
								elseif(@$getsupplementarychangerequertdatas > 0 && Auth::guard('student')->user()->can('supp_student_change_request_button') && @$getsupplementarychangerequertstudentdatas->is_department_verify == 3){
								$path = route('student_supp_change_requests',Crypt::encrypt(@$student_id));	
								$lists[] = array('lbl_text' =>'अपने पूरक आवेदन पत्र में अपडेट करने के लिए "Supplementary Change Request" बटन पर क्लिक करें।(To update your Supplementary form ,plase  click on " Supplementary Change Request" button)', 'btnText' => 'Supp. Change Request', 'is_new' => true, 'route' => $path);
							   }							   
							 
							?>
							@endcan
							@endif
							@endif
						   
							@if(@$isAllowAddEnrollment)
								@php 
									$lists[] = array('lbl_text' =>'यदि आपके पास अन्य स्ट्रीम और पाठ्यक्रमों में अन्य नामांकन संख्या है और आप अपनी एसएसओ आईडी के साथ लिंक करना चाहते हैं तो कृपया नामांकन जोड़ें बटन पर क्लिक करें।(If you have other enrollment number in other streams and courses and wish to link with your SSO ID please click on the "Add Enrollment" button.)', 'title' => 'Click here to Add other enrollment to with your sso.', 'btnText' => 'Add Enrollment', 'is_new' => true, 'route' => route('allreadystudent',Crypt::encrypt(@$current_student_ssoid)));
								@endphp
							@endif 
							@php
								if(@$isAllowToUpdateAiCode && $isAllowToUpdateAiCode == true){
									$lists[] = array('lbl_text' =>'यदि आपको पहले चुने गए AI केंद्र में कोई समस्या है, तो कृपया उसके विवरण को अपडेट करें।(If you have any issues with the AI center previously selected, feel free to update the details accordingly.)', 'title' => 'Click here to Update Aicode', 'btnText' => 'Update AiCentre', 'is_new' => true, 'route' => route('studentupdateaicenter',Crypt::encrypt(@$student_id)));	
								}
							@endphp
                            @if(@$lists)
                            <table class="table collection waves-color-demo">
                                @foreach(@$lists as $k => $item)
									@php 
										$customCls = "btn white-text secondary-content waves-yellow";
										if(@$item["customCls"] && !empty($item["customCls"])){
											$customCls=$item["customCls"];
										}
									@endphp
                                    <tr>
                                        <td  style="line-height: 25px;color:#0e0e88;font-size:20px;">
                                            <span class="language-markup" style="color: #0e0e88">
                                                 <!--{{ @$k+1 }}.  --> 
                                               
                                                @if(@$item['is_new'])
                                                    <img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="30" alt="materialize logo"/>
                                                @endif 
												
                                                @if(@$item['color'])
                                                    <span style="color:#0e0e88">
                                                        {{ @$item['lbl_text'] }}
                                                    </span>
                                                @else
                                                    {{ @$item['lbl_text'] }}
                                                @endif
                                                 @if(@$item['noteText'])
													 <br>
												 <br>
												 <span style="color:red;">Note:{{@$item['noteText']}}
												</span>
												
												@endif
                                            </span>
                                        </td>
										
                                        @if(@$item['btnText'])
                                          <td width="25%" style="text-align: center;">
                                            <a href="{{ @$item['route'] }}" title="{{  @$item['title'] }}" target="{{  @$item['target'] }}" class="{{ $customCls }}">
											
                                                {{ @$item['btnText'] }}									
                                            </a>
											
											
											
											
                                        </td>
										
										@endif
                                    </tr>
                                @endforeach
                            </table> 
							@else
                            <center>
                                <img src="data:image/jpeg;base64,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J1KsSey6/I3UzTqk/ddfkVq/wiaPpUrU3Z020w4S4NF0GAMYAgHE7sScV1c5rkOuemKVrrU30nTEgxtGxyRBlzWncd27hn134izjU9LU9pKoIDQ7WP8AZyxVBAaHax/s5Z6TIVdT0gw2hzL2Ja1owMFzTULheiJEjCePAqX8Sbxqelqe0oNHQjG1doHGLxddOPKJnFxxIkzxnfGC06pqjL0YGt54b45qLQ2+aeXerhERXKCIiIiIiIiqtYnVhZ6hs/0sC7EExeF6AfCu3o6VWanNtewiqbsOhgqMN64AM5IIEzE49kLaEUcvazT8laKsUjTyjWZi/dPBRrtXx6fmO9tLtXx6fmO9tSUUlUo12r49PzHe2tStJt/7QbAcaQc0AhsUtkQ0vk4wcDmZkCMIW7IsESoPZmi5EGbe7uRERZU0RERERERERERERERFgq2ZjucxrutoP4qN+xrNN74tRnjs2T3wrBEFtEN18AhfURERERERERERQ9I2Flak+lUEse0tduMEbjuPSpiIi53V+C6n4Nof0SxpPaQRPcolb4MTjdqzwkD19i6ei2Ot1vaKv61V9pctsfwc1g8XntAnFwOIG+AMycsenqXUkRVPe55lxkqp73PMuMlERFBRREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREX//Z" data-deferred="1" class="rg_i Q4LuWd" jsname="Q4LuWd" width="400" height="290" alt="Practice sets - The Keyword" data-iml="914.8000000007451" data-atf="true">
                            </center>
				    @endif

           </div>
        </div>
      </div>
	  </div>
            </div>
        </div>
         		
    </div>
    </div>
@endsection 