@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">     
       	<div class="col s12">
			<div class="container">
				<div class="col s12 m12 l12">
					<div class="row">
						<style> 
							.odd{
								background:white !important;
							}
							.even{
								background:rgba(242,242,242,.7) !important;
							}  
							.lower_odd{
							}
							.lower_even{
							} 

						</style>
						<fieldset>
							<legend>छात्र दस्तावेजों का सत्यापन(Verification of student documents)</legend>
							@php 
								$mainRowSpan = "4";
							@endphp
							<table class="striped responsive-table">
								<tr style="">
									<th style="text-align:center;">क्रमांक<br>(Sr.No.)</th>
									<th style="text-align:center;">दस्तावेज़ का नाम<br>(Document's Name)</th>
									<th style="text-align:center;">दस्तावेज़ देखें<br>(Document's View)</th>
									<th style="text-align:center;">दस्तावेज़ की स्थिति<br>(Document's status
										)<br> (
										Accept/Objection)</th>
									<th style="text-align:left;">फ़ील्ड जांचें<br>(Filled Info)</th>
									<th style="text-align:left;">फ़ील्ड जांचें<br>(Check field)</th>
									<th style="text-align:left;">फ़ील्ड पर कार्रवाई<br>(Action on the field
										)</th>  
								</tr> 
								
								{{-- Doc 1 --}}
								<tr class="odd">
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">1</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">जन्मतिथि प्रमाणपत्र</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}"><i class="material-icons">description</i>
									</td>
									<td rowspan="{{ @$mainRowSpan }}" style="text-align:center;">
										<span class="chip lighten-5 green green-text">
											Accepted
										</span>
									</td>
									<td>
										two two test Jian jain
									</td>
									<td style="text-align:left;">नगर निगम/परिषद/ ग्राम पंचायत/नगर पालिका/जन्म मृत्यु रजिस्ट्रार के हस्ताक्षर एवं सील हो (जन्म प्रमाण पत्र)
  <br>
										अगर निजी विद्यालय है तो सेमिस कोड/युडाइस कोड/पीईईओ/जिला शिक्षा से प्रमाणित अन्य राज्य की टी. सी. जिला शिक्षा अधिकारी से प्रमाणित(टी. सी.)
										<br>									 
										राजकीय सेवा मे कार्यरत कर्मचारी द्वारा सेवा पुस्तिका की कॉपी कार्यालय अध्यक्ष द्वारा प्रमाणित(सेवा पुस्तिका की कॉपी)
										<br>
										शपथ पत्र (अनाथ, फुटपाथी, राजकीय बालिका गृह की बालिका के लिए) नॉटेरी/सबडिवीजनल मजिस्ट्रेट/कार्यपालक मजिस्ट्रेट/चिकित्सा विधि प्रमाण पत्र द्वारा जारी हुआ हो
										<br>
										10 वीं अनुत्तीर्ण की मार्कशीट (75 बोर्ड से अनुत्तीर्ण अभ्यर्थी के लिए)</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr> 
								<tr class="odd"> 
									<td>
										Three two test Jian jain
									</td>
									<td style="text-align:left;">पिता का नाम</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>
								<tr class="odd"> 
									<td>
										Three two test Jian jain
									</td>
									<td style="text-align:left;">
										Mother's Name 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr class="odd">
								<tr class="odd"> 
									<td>
										Three two test Jian jain
									</td>
									<td style="text-align:left;">
										IS Govt. Issued Doc 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>


								{{-- Doc 2 --}}
								<tr class="even">
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">2</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">लिंग प्रमाण पत्र</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}"><i class="material-icons">description</i>
									</td>
									<td rowspan="{{ @$mainRowSpan }}" style="text-align:center;">
										<span class="chip lighten-5 red red-text">
											Objection
										</span>
									</td>
									<td>
										Three two test Jian jain
									</td> 
									<td style="text-align:left;">Gender Name</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr> 
								<tr class="even"> 
									<td style="text-align:left;">पिता का नाम</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>
								<tr class="even"> 
									<td style="text-align:left;">
										Mother's Name 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr class="even">
								<tr class="even"> 
									<td style="text-align:left;">
										IS Govt. Issued Doc 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in"  />
											<span></span>
											</label>
										</p>
									</td>
								</tr>


								{{-- Doc 3 --}}
								<tr class="odd">
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">2</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}">पता प्रमाण प्रमाण पत्र</td>
									<td style="text-align:center;" rowspan="{{ @$mainRowSpan }}"><i class="material-icons">description</i>
									</td>
									<td rowspan="{{ @$mainRowSpan }}" style="text-align:center;">
										<span class="chip lighten-5 red red-text">
											Objection
										</span>
									</td>
									<td style="text-align:left;">Gender Name</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in"/>
											<span></span>
											</label>
										</p>
									</td>
								</tr> 
								<tr class="odd"> 
									<td style="text-align:left;">पिता का नाम</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>
								<tr class="odd"> 
									<td style="text-align:left;">
										Mother's Name 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>
								<tr class="odd"> 
									<td style="text-align:left;">
										IS Govt. Issued Doc 
									</td>
									<td style="text-align:left;">
											<p> 
											<label>
											<input type="checkbox" class="filled-in" checked="checked" />
											<span></span>
											</label>
										</p>
									</td>
								</tr>

								{{-- Buttons --}}
								<tr class=""> 
									<td colspan="10">
										<br> 
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right btn_disable " type="submit" name="action"> Submit Verification
											</button>
										</div>
										<div class="col m2 s10 mb-3">
										  <a href="{{@$route}}" class="btn cyan waves-effect waves-light right">Reset</a> 
										</div> 
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection 
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/graphical/box.js') !!}"></script> 
@endsection