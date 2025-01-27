@extends('layouts.default')
@section('content')
	@php $role_id = @Session::get('role_id'); @endphp
	@if(@$role_id)
	<div id="main">
		<div class="row">
		@else
			<div id="main2">
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
							<div id="Form-advance" class="card card card-default scrollspy">
								@php //dd($student);
								@endphp
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
									<div class="card-content">
										 
										<?php 
										if(@$student->marksheet_migration_requests->challan_tid && $student->marksheet_migration_requests->challan_tid != ""){ ?>
											<?php 
												if(@$student->marksheet_migration_requests->locksumbitted && @$student->marksheet_migration_requests->locksubmitted_date){  ?>
													<div class="">
														<p style="color:green;font-size:22px;">
															पेमेंट के बाद आप डाउनलोड कर लें 
															संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन फॉर्म पीडीएफ प्रारूप में। 
															(After payment, you download the 
															Revised/Duplicate Marksheet/Migration form pdf format.)
														</p>
														<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
														<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
														style ="" 
														href="{{ route('reval_generate_student_pdf',Crypt::encrypt(@$marksheet_id)) }}">
															Download Revised/Duplicate Marksheet/Migration  Payment PDF
														</a> 
														<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
														style ="" 
														href="{{ route('landing') }}">
															Back
														</a> 
													</div> 
											<?php }  ?>
										<?php }  ?>
									</div>

									<div class="row">
										<div class="input-field col s4">
											@php 
											 
											$lbl='Enrollment No.'; $fld='enrollment'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												{!! @$student->$fld !!}
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
											@php $lbl="DOB (DD-MM-YYYY)"; $fld='dob'; @endphp
											<h8>
												{!!Form::label($fld, $lbl) !!}
												<?php 
													echo date("d-m-Y",strtotime($student->$fld));
													?>
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
											@php  
												$lbl="Total"; $fld='total_fees'; @endphp
											<h8>
												<span style="color:#00bcd4;font-size: 25px;">
													{!!Form::label($fld, $lbl,array('style' => 'font-size: 25px;color:#00bcd4;')) !!}
												@php 
													$fldTotal='total_fees';
													$fldLateFee='late_fees';
													$withoutLateFee = $student->marksheet_migration_requests->$fldTotal;
													echo $withoutLateFee . "/-";
												@endphp
											</span>
											</h8>
										</div> 
									</div>  
									<div class="row">
										<div class="input-field col s2">
											&nbsp;
										</div>
										<div class="input-field col s8" style="font-size:16px;">
											@php 
												$lbl = __('Grand Total of Marksheet Correction Form filling <b><u> Fee Payment Amount</b></u>'); $lblhi = __('(पूरक आवेदन पत्र भरने का शुल्क <b><u>भुगतान राशि का कुल योग</b></u>)');
												$fld='total_fees'; 
											@endphp 
											<span style="color:green;">
												{!! @$lbl . " : <span style='font-size:30px;''> Rs. " . @$student->marksheet_migration_requests->total_fees  . "/-</span> <br>" .  $lblhi . " : <span style='font-size:30px;'>रुपये " . @$student->marksheet_migration_requests->total_fees!!}/- </span>
											</span> 
										</div>
									</div> 
								</div>
								<!-- Fee payment button start -->
									<div class="card-content">
										@php $fld='makepayment'; @endphp 
										@if( @$student->marksheet_migration_requests->challan_tid == "")
											<div class="card-content invoice-print-area">
												<div class="row">
													<div class="col m12 s12">
														@php $lbl='Make Payment'; $fld='makepayment'; @endphp 
														@php $lbl1='Declaration'; $fld1='Declaration'; @endphp 
														{{ Form::open(['url'=>url()->current(),'id'=>"marksheet_correction"]) }} 
														{!! Form::token() !!}
														{!! method_field('PUT') !!} 
														<div class="col m7 s12 mb-3">
															<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> 
																Make Payment
															</button>
														</div> 
														{{ Form::close() }}
													</div>
												</div>
											</div>
										@endif
 
										<div class="card-content">
											<?php  
												if($isAlreadyRaisedRequest > 0 && $student->marksheet_migration_requests->challan_tid == "" ){ ?>
											<br/>
											<hr/>
											<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
											 	 
											In case your transaction got failed, To verify your payment, please click on <b>Verify  Your Payment</b> button.
											<br>
											यदि आपका लेन-देन विफल हो जाता है, तो अपना भुगतान सत्यापित करने के लिए, कृपया क्लिक करें
											<b>अपना भुगतान सत्यापित करें</b> बटन.
											<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
											style ="" href="{{ route('marksheet_verify_request',Crypt::encrypt(@$student->enrollment)) }}">
												Verify Your Payment
											</a> 
 
											<br/>
											<br/>
											<?php
											 
											
											if( $issueCount <= 0){ ?>
												<hr/>
												<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
											
												In case you want to raise a request to admin for your transaction got failed, please click on the raise a request button.
													<br>
													
													 
													यदि आप अपने लेन-देन के लिए व्यवस्थापक से अनुरोध करना चाहते हैं तो विफल हो गया, कृपया अनुरोध बढ़ाएँ <b>  बटन </b>  पर क्लिक करें।
													
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange"  href="{{ route('marksheet_raise_request',Crypt::encrypt(@$student->enrollment)) }}">
											Raise Payment Request For Payment
											</a>
											<?php }else{
													echo "<span style='color:green;font-size:20px;'>आपके विफल लेनदेन के लिए व्यवस्थापक से आपका अनुरोध सबमिट कर दिया गया है। (Your request to admin for your failer transaction has been submitted.)</span>";
												} 
											}?>
										</div>
										 
										<div class="card-content">
											<?php   
											if(@$student->marksheet_migration_requests->challan_tid && $student->marksheet_migration_requests->challan_tid != ""){ ?>
												<?php 
													if(@$student->marksheet_migration_requests->locksumbitted && @$student->locksubmitted_date){ ?>
														<div class="">
															<p style="color:green;font-size:22px;">
																पेमेंट करने के बाद आप एप्लीकेशन फॉर्म पीडीफ़ प्रारूप में डाउनलोड कर लें। 
																(After payment, you download the reval application's form pdf format.)
															</p>
															<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="40" alt="materialize logo"/>
															<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
															style ="" 
															href="{{ route('reval_generate_student_pdf',Crypt::encrypt(@$student->reval_students->student_id)) }}">
																Download Reval Payment PDF
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
<script>
	var application_fee = "@php echo $application_fee @endphp"; 
</script>
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/reval_payment/marksheet_registration_fee.js') !!}"></script> 
@endsection