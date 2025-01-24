@php
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
@endphp
@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="card-content">
				<div id="view-available-colors">
					<div id="cards-extended">
						<div class="card">
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
							</h4>
								
								
								
								
								<?php  
									$lists = array();
									if($auth_users != null){ 
										$lists[] = array('lbl_text' =>'1. 10वीं हिंदी के लिए बुक स्टॉक की जानकारी डाउनलोड करें(Book Stock Information Download For 10th Hindi)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryBookStockInformationDownloadExcel',1)); 
										$lists[] = array('lbl_text' =>'2. 10वीं अंग्रेजी के लिए बुक स्टॉक की जानकारी डाउनलोड करें(Book Stock Information Download For 10th English)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryBookStockInformationDownloadExcel',2)); 
										$lists[] = array('lbl_text' =>'3. 12वीं हिंदी के लिए बुक स्टॉक की जानकारी डाउनलोड करें(Book Stock Information Download For 12th Hindi)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryBookStockInformationDownloadExcel',3)); 
										$lists[] = array('lbl_text' =>'4. 12वीं अंग्रेजी के लिए बुक स्टॉक की जानकारी डाउनलोड करें(Book Stock Information Download For 12th English)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryBookStockInformationDownloadExcel',4)); 
									} 
								?> 
								
								<div style="margin-left:15px;">
									<table class="table collection waves-color-demo">
										<h5>
											पुस्तकों की <u>स्टॉक</u> के अनुसार जानकारी(Details of Books According to <u>Stock</u>)
										</h5>
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
								</div> 
							
								<?php  
									$lists = array();
									if($auth_users != null){ 
										$lists[] = array('lbl_text' =>'1. 10वीं हिंदी के लिए बुक आवश्यकता  की जानकारी डाउनलोड करें(Book Requirements Information Download For 10th Hindi)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryRequriedBookInformationDownloadExcel',1)); 
										$lists[] = array('lbl_text' =>'2. 10वीं अंग्रेजी के लिए बुक आवश्यकता  की जानकारी डाउनलोड करें(Book Requirements Information Download For 10th English)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryRequriedBookInformationDownloadExcel',2)); 
										$lists[] = array('lbl_text' =>'3. 12वीं हिंदी के लिए बुक आवश्यकता  की जानकारी डाउनलोड करें(Book Requirements Information Download For 12th Hindi)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryRequriedBookInformationDownloadExcel',3)); 
										$lists[] = array('lbl_text' =>'4. 12वीं अंग्रेजी के लिए बुक आवश्यकता  की जानकारी डाउनलोड करें(Book Requirements Information Download For 12th English)',  'btnText' => 'Download', 'is_new' => true, 'route' => route('summaryRequriedBookInformationDownloadExcel',4)); 
									} 
								?> 
								
								
								<div style="margin-left:15px;">
									<table class="table collection waves-color-demo">
										<h5>
											पुस्तकों की <u>आवश्यकता</u> के अनुसार जानकारी(Details of Books According to <u>Requirements</u>)
										</h5>
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
								</div>
								
								
								
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
</div>
@endsection