
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">व्यक्तिगत विवरण(Personal Details)</a></span></span></div>

<table border="1" style="width:100%;">
		<tr>
			<td><span class="font"><span class='customStrong'>आवेदक का नाम (Applicant&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->name}}</span></td>
			
			<td><span class="font"><span class='customStrong'>जन्म तिथि (Date of Birth)  </br>(DD-MM-YYYY):</span></span></td>
			<td><span class="font">&nbsp;{{ date('d-m-Y', strtotime(@$masterDetails->dob))}}</span></td>
		</tr>
		<tr>
		<td><span class="font"><span class='customStrong'>पिता का नाम (Father&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->father_name}}</span></td>
		
			<td><span class="font"><span class='customStrong'>माँ का नाम (Mother&#39;s Name): </span></span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->mother_name}}</span></td>
			</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>लिंग(Gender):</span> </span></td>
			<td><span class="font">&nbsp;{{@$gender_id[@$masterDetails->gender_id]}}</span></td>
		
			<td><span class="font"><span class='customStrong'>श्रेणी  ए (Category A):</span> </span></td>
			<td><span class="font">&nbsp;{{@$categorya[@$masterDetails->application->category_a]}}</span></td>
			
			
			
		</tr>
		<tr>
		    <td><span class="font"><span class='customStrong'>पाठ्यक्रम(Course):</span> </span></td>
			<td><span class="font">&nbsp;{{@$course[@$masterDetails->course]}}</span></td>
			<td><span class="font"><span class='customStrong'> स्ट्रीम(Stream):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{@$stream_id[@$masterDetails->stream]}}</span></td>

			
		</tr>
        <tr>
		    <td><span class="font"><span class='customStrong'>छात्र द्वारा स्वयं भरा गया है? (Is it filled by the student themselves?):</span> </span></td>
                		
<td><span class="font">&nbsp;{{@$yesno[@$masterDetails->is_self_filled]}}</span></td>
			<td><span class="font"><span class='customStrong'> एसएसओ (SSO):</span> </span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->ssoid}}</span></td>
		</tr>

		<tr>
		   <td><span class="font"><span class='customStrong'>विभिन्न संकाय विषय(Multiple Faculty Subjects):</span> </span></td>
			<td><span class="font">&nbsp;{{@$yesno[@$masterDetails->application->is_multiple_faculty]}}</span></td>

			<td><span class="font"><span class='customStrong'>मुख्य संकाय(Preferred Faculty):</span>&nbsp;</span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->application->selected_faculty}}</span></td>
			
		</tr>
		<tr>
		   <td><span class="font"><span class='customStrong'>धर्म(Religion):</span> </span></td>
			<td><span class="font">&nbsp;{{@$religion[@$masterDetails->application->religion_id]}}</span></td>
		
			<td><span class="font"><span class='customStrong'>राष्ट्रीयता (Nationality):</span>&nbsp;</span></td>
			<td><span class="font">&nbsp;{{@$nationality[@$masterDetails->application->nationality]}}</span></td>
			
		</tr>
		<tr>
			<td>
				<span class="font"><span class='customStrong'>वंचित वर्ग(Disadvantage Group):</span></span>
			</td>
			<td>
				<span class="font">&nbsp;{{@$dis_adv_group[@$masterDetails->application->disadvantage_group]}}</span>
			</td>
			<td><span class="font"><span class='customStrong'>दिव्यांगता(Disability):</span> </span></td>
			<td><span class="font">&nbsp;{{@$disability[@$masterDetails->application->disability]}}</span></td>
		</tr>
		
		<tr>
			
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>शहरी/ग्रामीण(Rural/Urban):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{@$rural_urban[@$masterDetails->application->rural_urban]}}</span></td>
			<td><span class="font"><span class='customStrong'>आधार नंबर (Aadhar Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->application->aadhar_number}}</span></td>
		</tr>
		<tr>
			
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>मोबाइल नंबर (Mobile Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->mobile}}</span></td>
			<td><span class="font"><span class='customStrong'>ईमेल (Email):</span></span></td>
			<td><span class="font">&nbsp;{{@$masterDetails->email}}</span></td>
		</tr>
		  <td><span class="font"><span class='customStrong'>जन आधार संख्या </br>(Jan Aadhar Number)</span></span></td>
		  @if(!empty($masterDetails->application->jan_aadhar_number))
			<td><span class="font">&nbsp;{{@$masterDetails->application->jan_aadhar_number}}</span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif
			
			
			<td><span class="font"><span class='customStrong'>क्या आप राजस्थान के निवासी हैं?  </br>(Are You from Rajasthan?):</span> </span></td>
			<td><span class="font">&nbsp;{{@$are_you_from_rajasthan[@$masterDetails->are_you_from_rajasthan]}}</span></td>
		</tr>
	
		<tr>
		    @if(@$masterDetails->course == 12)
			<td><span class="font"><span class='customStrong'>10 वीं उत्तीर्ण होने का वर्ष </br>(10th Year of Passing):</span> </span></td>
			<td><span class="font">&nbsp;{{@$rsos_years[@$masterDetails->application->year_pass]}}</span></td>
			@endif
			<td><span class="font"><span class='customStrong'>पूर्व योग्यता(Previous Qualification):</span></span></td>
			<td><span class="font">&nbsp;{{@$pre_qualifi[@$masterDetails->application->pre_qualification]}}</span></td>
			 @if(@$masterDetails->course == 10)
			<td><span class="font"><span class='customStrong'>अध्ययन का माध्यम (Medium of Study):</span></span></td>
			<td><span class="font">&nbsp;{{@$midium[@$masterDetails->application->medium]}}</span></td>
			@endif
		</tr>
		 @if(@$masterDetails->course == 12)
		<tr>
			<td><span class="font"><span class='customStrong'>अध्ययन का माध्यम (Medium of Study):</span></span></td>
			<td><span class="font">&nbsp;{{@$midium[@$masterDetails->application->medium]}}</span></td>
		</tr>
		@endif
		<tr>
			<td><span class="font"><span class='customStrong'>क्या ओटीपी सत्यापित है?(Is OTP verified):</span></span></td>
			<td><span class="font">&nbsp;{{@$yesno[@$masterDetails->is_otp_verified]}}</span></td>
		</tr>
		
	
		
	</tbody>
</table>
@include('elements.verification.other_then_document_status_update')