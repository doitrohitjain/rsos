@extends('layouts.default')
@section('content')
	@if($isStudent || !empty(@$id = Auth::user()->id))
	<div id="main">
		<div class="row">
		@endif
		
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card">
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div class="card-content">
								<?php  
									if($isAlreadyRaisedRequest > 0 && $student->challan_tid == "" ){ ?>
								<br/>
								<hr/>
								<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
										
								In case your transaction got failed, To verify your payment, please click on <b>Verify  Your Payment</b> button.
								<br>
								यदि आपका लेन-देन विफल हो जाता है, तो अपना भुगतान सत्यापित करने के लिए, कृपया क्लिक करें
								<b>अपना भुगतान सत्यापित करें</b> बटन.
								<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
								style="" href="{{ route('verify_request',Crypt::encrypt(@$student->id)) }}">
									Verify Your Payment
								</a> 

								<br/>
								<br/>
								<?php if( $issueCount <= 0){ ?>
									<hr/>
									<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
								
									In case you want to raise a request to admin for your transaction got failed, please click on the raise a request button.
										<br>
										
											
										यदि आप अपने लेन-देन के लिए व्यवस्थापक से अनुरोध करना चाहते हैं तो विफल हो गया, कृपया अनुरोध बढ़ाएँ <b>  बटन </b>  पर क्लिक करें।
										
										<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange"  href="{{ route('raise_request',Crypt::encrypt(@$student->id)) }}">
								Raise Payment Request For Payment
								</a>
								<?php }else{
										echo "<span style='color:green;font-size:20px;'>आपके विफल लेनदेन के लिए व्यवस्थापक से आपका अनुरोध सबमिट कर दिया गया है। (Your request to admin for your failer transaction has been submitted.)</span>";
									} 
								}?>
							</div>
							<div id="Form-advance" class="card card card-default scrollspy">
								@php //dd($student);
								@endphp
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
									<div class="card-content">
										@php 
											$fld = 'application_fee';
										@endphp
										@if(@$$fld <= 0)
											@php  
												echo "<span style='color:red;font-size:24px;'>आपको शुल्क का भुगतान करने की अनुमति नहीं है क्योंकि आपकी आवेदन शुल्क राशि " . @$$fld . " है। (You are not allowed to pay fees because your application fee amount is " . @$$fld . ".)</span>";
												die;
											@endphp
										@endif 
										<?php


										if(@$student->challan_tid && $student->challan_tid != ""){ ?>
											<?php 
												if(@$student->application->locksumbitted && @$student->application->locksubmitted_date){ ?>
													<div class="">
														<p style="color:green;font-size:22px;">
															पेमेंट करने के बाद आप एप्लीकेशन फॉर्म पीडीफ़ प्रारूप में डाउनलोड कर लें। 
															(After payment, you download the application's form pdf format.)
														</p>
														<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
														<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
														style ="" 
														href="{{ route('generate_student_pdf',Crypt::encrypt(@$student->id)) }}">
															Download Payment PDF
														</a> 

														@php 
															$SSO_URL_DASHBOARD = Config::get('global.SSO_URL_DASHBOARD'); 
														@endphp 
														<a href="{{ @$SSO_URL_DASHBOARD }}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
															Go To SSO
														</a>
													
													</div> 
											<?php }  ?>
										<?php }  ?>
									</div>
                      
									<div class="row">
										@php $lbl='Enrollment No.'; $fld='enrollment'; @endphp
										@if(@$student->$fld)
											<div class="input-field col s4">
												<h8>
													{!!Form::label($fld, $lbl) !!}
													{!! @$student->$fld !!}
												</h8>
											</div>
										@endif
										<div class="input-field col s4">
											@php $lbl='Aadhar Number'; $fld='aadhar_number'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$student->application->$fld !!}
											</h8>
										</div>
										<div class="input-field col s4">
											@php $lbl='Name'; $fld='name'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$student->$fld !!}
											</h8>
										</div>  
										<div class="input-field col s4">
											@php $lbl="Father's Name"; $fld='father_name'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$student->$fld !!}
											</h8>
										</div>
										<div class="input-field col s4">
											@php $lbl="DOB"; $fld='dob'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$student->$fld !!}
											</h8>
										</div> 
										<div class="input-field col s4">
											@php $lbl="Course"; $fld='course'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$course[$student->$fld] !!}
											</h8>
										</div>
										<div class="input-field col s4">
											@php $lbl="Stream"; $fld='stream'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$stream_id[$student->$fld] !!}
											</h8>
										</div>
										
										<div class="input-field col s4">
											@php $lbl="Total"; $fld='total'; @endphp
											<h8>
												<span style="color:#00bcd4;font-size: 25px;">
													{!!Form::label($fld, $lbl,array('style' => 'font-size: 25px;color:#00bcd4;')) !!}
												@php 
													$fldTotal='total';
													$fldLateFee='late_fee';
													$withoutLateFee = $student->studentfee->$fldTotal - $student->studentfee->$fldLateFee;
													echo $withoutLateFee . "/-";
												@endphp
											</span>
											</h8>
										</div> 

										@if($student->studentfee->$fldLateFee > 0)
											<div class="input-field col s4">
												@php $lbl="Late Fees"; $fld='late_fee'; @endphp
												<h8>
													<span style="color:red;font-size: 25px;">
														{!!Form::label($fld, $lbl,array('style' => 'font-size: 25px;color:red;')) !!}
													{!! $student->studentfee->$fld .  "/-" !!}
												</span>
												</h8>
											</div> 
										@endif
									</div>  
									<div class="row">
										<div class="input-field col s2">
											&nbsp;
										</div>
										<div class="input-field col s8" style="font-size:16px;">
											@php 
												$lbl = __('Grand Total of Application Form filling <b><u> Fee Payment Amount</b></u>'); $lblhi = __('(आवेदन पत्र भरने का शुल्क <b><u>भुगतान राशि का कुल योग</b></u>)');
												$fld='application_fee'; 
											@endphp 
											<span style="color:green;">
												{!! @$lbl . " : <span style='font-size:30px;''> Rs. " . @$$fld  . "/-</span> <br>" .  $lblhi . " : <span style='font-size:30px;'>रुपये " . @$$fld !!}/- </span>
											</span> 
										</div>
									</div> 
								</div> 
								
								<!-- Fee payment button start -->
									<div class="card-content">
										@php $fld='makepayment'; @endphp 
										<?php 
										// @dd($feePaymentAllowOrNotStatus);
										if($student->challan_tid=="" && $feePaymentAllowOrNotStatus == 'true'){
										// @if($student->challan_tid=="" && $feePaymentAllowOrNotStatus == 'true')	
										?>
											<div class="card-content invoice-print-area">
												<div class="row">
													<div class="col m12 s12">
														@php $lbl='Make Payment'; $fld='makepayment'; @endphp 
														@php $lbl1='Declaration'; $fld1='Declaration'; @endphp 
														{{ Form::open(['route' => [request()->route()->getAction()['as'],$estudent_id], 'id' =>$model]) }} 
														{!! Form::token() !!}
														{!! method_field('PUT') !!} 
														<div class="col m7 s12 mb-3">
															<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> 
																{{ $lbl }}
															</button>
														</div> 
														{{ Form::close() }}
													</div>
												</div>
											</div>
										<?php }  ?>
 
										<div class="card-content">
											<?php  
												if($isAlreadyRaisedRequest > 0 && $student->challan_tid == "" ){ ?>
											<br/>
											<hr/>
											<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
											 	 
											In case your transaction got failed, To verify your payment, please click on <b>Verify  Your Payment</b> button.
											<br>
											यदि आपका लेन-देन विफल हो जाता है, तो अपना भुगतान सत्यापित करने के लिए, कृपया क्लिक करें
											<b>अपना भुगतान सत्यापित करें</b> बटन.
											<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
											style ="" href="{{ route('verify_request',Crypt::encrypt(@$student->id)) }}">
												Verify Your Payment
											</a> 
 
											<br/>
											<br/>
											<?php if( $issueCount <= 0){ ?>
												<hr/>
												<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
											
												In case you want to raise a request to admin for your transaction got failed, please click on the raise a request button.
													<br>
													
													 
													यदि आप अपने लेन-देन के लिए व्यवस्थापक से अनुरोध करना चाहते हैं तो विफल हो गया, कृपया अनुरोध बढ़ाएँ <b>  बटन </b>  पर क्लिक करें।
													
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange"  href="{{ route('raise_request',Crypt::encrypt(@$student->id)) }}">
											Raise Payment Request For Payment
											</a>
											<?php }else{
													echo "<span style='color:green;font-size:20px;'>आपके विफल लेनदेन के लिए व्यवस्थापक से आपका अनुरोध सबमिट कर दिया गया है। (Your request to admin for your failer transaction has been submitted.)</span>";
												} 
											}?>
										</div>
										 
										<div class="card-content">
											<?php  
											if(@$student->challan_tid && $student->challan_tid != ""){ ?>
												<?php 
													if(@$student->application->locksumbitted && @$student->application->locksubmitted_date){ ?>
														<div class="">
															<p style="color:green;font-size:22px;">
																पेमेंट करने के बाद आप एप्लीकेशन फॉर्म पीडीफ़ प्रारूप में डाउनलोड कर लें। 
																(After payment, you download the application's form pdf format.)
															</p>
															<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
															<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
															style ="" 
															href="{{ route('generate_student_pdf',Crypt::encrypt(@$student->id)) }}">
																Download Payment PDF
															</a> 
														</div> 
												<?php }  ?>
											<?php }  ?>
										</div>

									</div>
								<!-- Fee payment button end -->
								</div>
						</div>
					</div>
				</div>
			</div>
		@if(!empty(@$id = Auth::user()->id))
		</div>
	</div>
	@endif
@endsection 
@section('customjs')
<script>
	var application_fee = "@php echo $application_fee @endphp"; 
</script>
	<script src="{!! asset('public/app-assets/js/bladejs/payment/registration_fee.js') !!}"></script> 
@endsection