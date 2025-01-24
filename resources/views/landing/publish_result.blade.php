<style>
@media screen and (min-width: 480px) {
    
	logocss{
		align:center
	}
}
</style>
@php

$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
 
@endphp

@extends('layouts.guest')
@section('content')

<div class="row">
    <div class="col s12">
        <div class="card-content">
          <div id="view-available-colors">
			<div id="cards-extended">
				<div class="card"> 
					<div class="card-content">
						<center>
							<div class="">
								<span style="color:#00bcd4;font-size:20px;padding-top:20px;">
									 <a href="{{ route('landing')}}"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="Logo" width="40px" height="40px"/></a>
								</span>
								
								<span style="color:#00bcd4;font-size:30px;padding-top:0px;font-weight:bold;">
									@php echo Config::get('global.siteTitle'); @endphp
								</span>
							</div> 
						</center>	
					</div>
	 
 
      	<h4 class="card-title">
			<div class="row">
				 
									@php 
									$auth_users=Auth::user();
									if($auth_users==null){
									$auth_users=Auth::guard('student')->user();
									}					
									@endphp
							@if(@$auth_users)
								</br></br>
			                <div class="step-actions right">
									<div class="col m2 s12 mb-1">
									          @php 
												$auth_users=Auth::user();
												if($auth_users==null){
												$auth_users=Auth::guard('student')->user();
												$auth_users = null;
												}					
												@endphp
												@if(@$auth_users)
												<a href="{{route('logout')}}" class=" btn submitBtnCls submitconfirms waves-effect waves-teal btn white-text secondary-content">
											    logout
												</a>
												@endif
											</div>
											
											<div class="col m5 s12 mb-2">
												@php 
												$auth_users=Auth::user();
												if($auth_users==null){
												$auth_users=Auth::guard('student')->user();
												}					
												@endphp
												@if(@$auth_users)
												<a href="{{route('dashboard')}}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
												My Dashboard
												</a>
												@endif
											</div>
											<div class="col m4 s12 mb-2">
												@php 
												$SSO_URL_DASHBOARD = Config::get('global.SSO_URL_DASHBOARD'); 
												@endphp
											 <a href="{{route('landing')}}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
										Back
									     </a>
											</div>
										</div>
								 @else
						<div class="col 14 s12 m12"> 
					  <table>
						<tr style="border: none;" >								
							<td >
								@php 
								$SSO_URL_DASHBOARD = Config::get('global.SSO_URL_DASHBOARD'); 
									@endphp
						
									 <a href="{{route('landing')}}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
										Back
									     </a>
							</td>
							
						</tr>
					</table>
				</div> 
										
				                @endif
				<!--<div class="col s12 m12"> 
					<table>
						<tr style="border: none;" >
                           <td style="width:none;">
								
									@php 
										$auth_users=Auth::user();
										if($auth_users==null){
											$auth_users=Auth::guard('student')->user();
										}					
									@endphp
									@if(@$auth_users)
									<a href="{{route('logout')}}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
										logout
									@endif
							</td>	
							 <td style="width:none;">
								
									@php 
										$auth_users=Auth::user();
										if($auth_users==null){
											$auth_users=Auth::guard('student')->user();
										}					
									@endphp
									@if(@$auth_users)
									<a href="{{route('dashboard')}}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
										My Dashboard
									</a>
									@endif
							</td>								
							<td >
								
									@php 
										$SSO_URL_DASHBOARD = Config::get('global.SSO_URL_DASHBOARD'); 
									@endphp
						
									<a href="{{ @$SSO_URL_DASHBOARD }}" class="waves-effect waves-teal btn gradient-90deg-deep-orange-orange white-text secondary-content">
										Go To SSO
									</a>
								
							</td>
							
						</tr>
					</table>
				</div> -->
			
			
			
			
			
		</h4>
			
			
		<?php  
			$lists = array();
			
			//$lists[] = array('lbl_text' => 'कक्षा 10वीं एवं 12वीं के विद्यार्थि प्रवेश हेतु आवेदन करने के लिए बटन पर क्लिक करें(Click on the button to apply for admission for class 10th & 12th admission. )',  'btnText' => 'Apply for Admission', 'is_new' => true, 'target' => '_blank', 'route' => @$SSO_URL_DASHBOARD );

			

			
			
			if(@$showSuppStatus == true ){
				//$lists[] = array('lbl_text' => 'पूरक प्रवेश शुल्क ऑनलाइन भुगतान के लिए बटन पर क्लिक करें(Click on the button for Supp. Adm Fee Online Payment)',  'btnText' => 'Make Supplementary Payment', 'is_new' => false, 'target' => '_blank', 'color' => 'blue','is_new' => true, 'route' => route('supp_admission_fee_payment'));
			}
				/* $lists[] = array('lbl_text' => 'Click here to login',  'btnText' => 'Make Payment', 'is_new' => true, 'target' => '_blank', 'color' => 'red', 'route' => route('login')); */

	/*
		 	//$lists[] = array('lbl_text' => 'लॉग इन के लिए कृपया बटन पर क्लिक करें(Please click on the button for Login)',  'btnText' => 'Login', 'is_new' => false,'target' => '_blank', 'route' => route('login')); 
			*/

			// $lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का पुनर्मूल्यांकन परिणाम प्रकाशित किया गया है, कृपया! पुनर्मूल्यांकन परिणाम देखने  के लिए  "View Result" बटन पर क्लिक करें। ( '.$result_session.' Reval Result has been published by Rajasthan State Open School, Please Click on the "View Result" button to view Reval Result. )',  'btnText' => 'View Result', 'is_new' => true, 'route' => route('result')); 
			
			if(@$showStatus == true && @$resultCheckStatus == 2 || @$resultCheckStatus==1){
				
				/* Cake Start */
				$lists[] = array('lbl_text' =>' Link 1. राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का परिणाम प्रकाशित किया गया है, कृपया! परिणाम देखने  के लिए  "View Result" बटन पर क्लिक करें। ( '.$result_session.' Result has been published by Rajasthan State Open School, Please Click on the "View Result" button to view result. )',  'btnText' => 'View Result', 'is_new' => true, 'route' => 'http://103.122.38.42/provisionalresult');
				/* Cake End */
				/* Larv 2nd Server Start */
				$lists[] = array('lbl_text' =>' Link 2. राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का परिणाम प्रकाशित किया गया है, कृपया! परिणाम देखने  के लिए  "View Result" बटन पर क्लिक करें। ( '.$result_session.' Result has been published by Rajasthan State Open School, Please Click on the "View Result" button to view result. )',  'btnText' => 'View Result', 'is_new' => true, 'route' => 'http://172.21.91.154/rsos/provisionalresult');
				/* Larv 2nd Server End */
				
				/* Larv 1st Server Start */
				$lists[] = array('lbl_text' =>'Link 3. राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का परिणाम प्रकाशित किया गया है, कृपया! परिणाम देखने  के लिए  "View Result" बटन पर क्लिक करें। ( '.$result_session.' Result has been published by Rajasthan State Open School, Please Click on the "View Result" button to view result. )',  'btnText' => 'View Result', 'is_new' => true, 'route' => route('result')); 
				/* Larv 1st Server End */ 
			}
			//$lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का परिणाम प्रकाशित किया गया है, कृपया! परिणाम देखने  के लिए  "View Result" बटन पर क्लिक करें। ( '.$result_session.' Result has been published by Rajasthan State Open School, Please Click on the "View Result" button to view result. )',  'btnText' => 'View Result', 'is_new' => true, 'route' => route('result')); 
			//$lists[] = array('lbl_text' => 'प्रवेश शुल्क ऑनलाइन भुगतान के लिए बटन पर क्लिक करें(Click on the button for Admission Fee Online Payment)',  'btnText' => 'Make Payment', 'is_new' => false, 'target' => '_blank', 'route' => route('admission_fee_payment'));
			// $lists[] = array('lbl_text' => 'प्रवेश के लिए आवेदन करने के लिए बटन पर क्लिक करें(Click on the button to apply for admission)',  'btnText' => 'Apply for Admission', 'is_new' => false, 'target' => '_blank', 'route' => route('admission_option'));
			

			

			/*if($showrevisedStatus == true && $resultCheckStatus == 2 || $resultCheckStatus==1){
				// $lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल द्वारा  ' .$result_session .' का संशोधित परिणाम प्रकाशित किया गया है, कृपया! संशोधित परिणाम देखने  के लिए  "View Revised Result" बटन पर क्लिक करें। ( '.$result_session.' Revised Result has been published by Rajasthan State Open School, Please Click on the "View Revised Result" button to view result. )',  'btnText' => 'View Revised Result', 'is_new' => true, 'route' => route('revisedresult')); 
				
			}*/
			if(@$showrevisedStatus == true && @$resultCheckStatus == 2 || @$resultCheckStatus==1){
				//$lists[] = array('lbl_text' =>' राजस्थान स्टेट ओपन स्कूल द्वारा घोषित पिछले वर्षों के परिणाम देखने के लिए कृपया "View Previous Year Result" बटन पर क्लिक करें।(Please click on "View Previous Year Result" button to view result of previous years result announced by Rajasthan State Open School.'.$allowYearCombo.')',  'btnText' => 'View Previous Year Result', 'is_new' => false, 'route' => route('resultprevious')); 
			} 
			
		
		?>
	 <table class="table collection waves-color-demo" style="border-bottom:1px solid #e0e0e0">
			 <!--<tr><td width="100%" style="line-height:25px;color:#0e0e88;font-size:20px;">वे अभ्यर्थी जिन्होंने पूर्व में AI Centre द्वारा फॉर्म भरवा कर फीस जमा करा दी है उन्हें सिर्फ अपनी SSOID से लॉगिन कर अपना enrollment,जन्म दिनांक एवं कॅप्टचा डाल अपनी जानकारी को चेक कर सिर्फ सबमिट ही करना है जिसके बाद वह अपनी सभी जानकारी डैशबोर्ड द्वारा जाँच कर सकते हैं</td></tr>
			<tr><td width="100%" style="line-height:25px;color:#0e0e88;font-size:20px;">पूर्व में AI Centre द्वारा भरे गए फॉर्म में अपनी SSOID को लिंक करने की प्रक्रिया के लिए <a href='public/videos/freshstudentformfilling.webm'target="_blank" style='color:red;'>यहाँ क्लिक करें </a>|(<a href='public/videos/allreadystudent.webm'target="_blank" style='color:red;'>Click here for </a>Steps to map your ssoid in previously filled form by AI Centre)</td></tr>
			<!--<tr><td width="100%" style="line-height:25px;color:#0e0e88;font-size:20px;">राजस्थान स्टेट ओपन स्कूल में प्रवेश  फॉर्म आवेदन सत्यापित करने की प्रक्रिया के लिए <a href='public/videos/applicationVerifier.mp4'target="_blank" style='color:red;'>यहाँ क्लिक करें |(<a href='public/videos/applicationVerifier.mp4'target="_blank" style='color:red;'>Click here </a>for the process to Application verify Rajasthan State Open School Admission Form Application.)</td></tr>
			<tr><td width="100%" style="line-height:25px;color:#0e0e88;font-size:20px;">राजस्थान स्टेट ओपन स्कूल में प्रवेश  फॉर्म आवेदन सत्यापित करने की प्रक्रिया के लिए <a href='public/videos/acadmic_verifier.mp4'target="_blank" style='color:red;'>यहाँ क्लिक करें |(<a href='public/videos/acadmic_verifier.mp4'target="_blank" style='color:red;'>Click here </a>for the process to Application verify Rajasthan State Open School Admission Form Application.)</td></tr>-->
		 </table>
		 <table class="table collection waves-color-demo">
		
						@foreach(@$lists as $k => $item)

				<tr>
					<td  width="75%" style="line-height: 25px;color:#0e0e88;font-size:20px;">
						<span class="language-markup" style="color: #0e0e88">
							 <!--{{ @$k+1 }}.  -->  
							@if(@$item['color'])
								<span style="color:#0e0e88">
									{{ @$item['lbl_text'] }}
								</span>
							@else
								{{ @$item['lbl_text'] }}
							@endif
							@if(@$item['is_new'])
								<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
							@endif
						</span>
					</td>
					<td width="25%" style="text-align: center;">
						<a href="{{ @$item['route'] }}" target="{{  @$item['target'] }}" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content">
							{{ @$item['btnText'] }}
						</a>
					</td>
				</tr>
			@endforeach
		</table> 
		<span class="red-text">
				<b>
					Note: This application is best viewed using current versions of Firefox, Chrome at a screen resolution of 1024 x 768 or higher.This application will not work in Internet explorer Ver 11 and above.
				</b>
			</span>
		</p> 
    </div>
  </div>
  </div>
    </div>
	  </div>
	    </div>
		  </div>
		 
 
       
@endsection 



<style>
	#main {
		padding-left: 0px !important;
	} 
	
</style>

