@extends('layouts.pdf')
@section('content')
<style type="text/css">

@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
table, th, td{
  border: 1px solid #D3D4D9;
  border-collapse: collapse;
  font-size:14px;
  font-family:Arial;
  
}
 .cc51 {
  font-size: 24px;
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}
.font{
 font-size:14px;
 font-family:Arial;
 font-weight:normal;  
 }
 .fonthead{
 font-size:12px;
 font-family:Arial;
 font-weight:bold;  
 }
 .right {
  margin-left: 70%;
}

</style>
</head>
<body style="margin-top:-10%,margin-bottom:0%">
	@if(@$isShowVerifcationPart)
		<span class="" style="float:right;">
			<span style="font-size:10px;"><b>Final Verification Status:</b></span>
			<span class="font ">
			
				@php $textColor = "red"; @endphp
				@if($finalVerifyStatus == "Approved")
					@php $textColor = "green"; @endphp
				@endif
				<span class="" style="color:{{ $textColor }};font-weight:700;">
					{{ @$finalVerifyStatus }}
				</span>
			</span>
		</span>
	@endif

<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 30px; border-radius: 10px" alt="image upload button"/>
  </div>  
RSOS Form - Academic Session {{@$exam_month[@$master->exam_month]}} {{@$admission_sessions[@$master->exam_year]}}
  
</div>&nbsp;
@if($master->application->category_a !=7)
@if(@$master->gender_id == 2 && @$master->are_you_from_rajasthan == 1)
<span style="color: red; font-size:14px;display:none;">Notes:- Online Service Fees of Rs. 30/- to be paid offline to the AI center </span>&nbsp;
@else
<span style="color: red; font-size:14px;display:none;">Notes:- Forwarding Fees of Rs. 50/- and Online Service Fees of Rs. 30/- to be paid offline to the AI center</span>&nbsp;
@endif
@endif
        @if(@$master->student_change_requests ==2 && @$changerequeststudent->student_update_application == 1)
		<div width="100%"><span style="font-size:16px; color:red;"><b>Note:</b></span> <span class="font" style="font-size:16px; color:red;">Change Request Not completed</span></div>&nbsp;&nbsp;
	    @endif
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td colspan="2" width="70%"> 
				<span style="font-size:13px;"><b>Enrollment Number:</b></span>
				<span class="font">{{@$master->enrollment}}</span>
			</td>
			<td colspan="" rowspan="5">
			<center>
				<table style="border:0px;">
					<tr>
						<td style="border:0px;">
						<span class="font">
							<img alt="Photo" height="60px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->photograph)}}" width="60px" />
						</span>
						</td>
						<td style="border:0px;">
						&nbsp;&nbsp;&nbsp;
						</td>
						<td style="border:0px;">
							<span>
								<img alt="Signature" height="60px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->signature)}}" width="60px" />
							</span>
						</td>
					</tr>
				</table>
				
				</center>
				
			</td>
			<!-- <td colspan=""> -->
				<!-- <span style="font-size:10px;"><b>Final Verification Status:</b></span>
				<span class="font">
					@php $textColor = "red"; @endphp
					@if($finalVerifyStatus == "Approved")
						@php $textColor = "green"; @endphp
					@endif
					<span class="" style="color:{{ $textColor }};font-weight:700;">
						{{ @$finalVerifyStatus }}
					</span>
				</span> -->
			<!-- </td> -->
		</tr>
		<tr>
			<td colspan="2" width="100%"><span style="font-size:13px;"><span><b>AI Center Name:</b></span> <span class="font">{{@$aiCenters[@$master->ai_code]}}</span></td>
		</tr>
		<tr>
			<td style="width: 70%;" colspan="2">
				<span style="font-size:13px;">
					<span class='customStrong'>
						<b>प्रवेश आवेदन का प्रकार(Admission Type) :
						</b>
					</span>
				</span>
				<span class="font" > {{@$adm_types[@$master->adm_type]}}</span>
			</td> 
		</tr>
		<tr>
			<td style="width: 70%;" colspan="1">
			<span style="font-size:13px;"><b>परीक्षा(Exam) : </b></span>
			 @if((@$exam_session[@$master->exam_month]))
				{{ $exam_session[@$master->exam_month] }}
			@endif
					</td>					
			
		</tr>
		@if(@$master->stream && $master->stream == 2)
		@else
			<tr>
				<td style="width: 70%;">
					<span style="font-size:13px;"><b>शिक्षण ई-सामग्री/किताबें ?(Types of learning e-content/books?) : </b></span>
					@if(!empty($master->book_learning_type_id))
					<span class="font">{{@$book_learning_type[@$master->book_learning_type_id]}}</span>
					@else
					<span class="font">N/A</span>
					@endif
				</td> 
			</tr>
		@endif 
	</tbody>
</table>

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">व्यक्तिगत विवरण(Personal Details)</a></span></span></div>

<table border="1" style="width:100%;">
		
		<tr>
			<td width="25%"><span class="font"><span class='customStrong'>आवेदक का नाम (Applicant&#39;s Name):</span></span></td>
			<td width="25%"><span class="font">&nbsp;{{@$master->name}}</span></td>
			
			<td width="25%"><span class="font"><span class='customStrong'>जन्म तिथि (Date of Birth)  <span style="font-size:10px;">(DD-MM-YYYY)</span>:</span></span></td>
			<td width="25%"><span class="font">&nbsp;{{ date('d-m-Y', strtotime(@$master->dob))}}</span></td>
			
			
			</tr>
		<tr>
		<td><span class="font"><span class='customStrong'>पिता का नाम (Father&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->father_name}}</span></td>
		
			<td><span class="font"><span class='customStrong'>माँ का नाम (Mother&#39;s Name): </span></span></td>
			<td><span class="font">&nbsp;{{@$master->mother_name}}</span></td>
			</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>लिंग(Gender):</span> </span></td>
			<td><span class="font">&nbsp;{{@$gender_id[@$master->gender_id]}}</span></td>
		
			<td><span class="font"><span class='customStrong'>श्रेणी  ए (Category A):</span> </span></td>
			<td><span class="font">&nbsp;{{@$categorya[@$master->application->category_a]}}</span></td>
			
			
			
		</tr>
		<tr>
		    <td><span class="font"><span class='customStrong'>पाठ्यक्रम(Course):</span> </span></td>
			<td><span class="font">&nbsp;{{@$course[@$master->course]}}</span></td>
			<td><span class="font"><span class='customStrong'> स्ट्रीम(Stream):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{$stream_id[@$master->stream]}}</span></td>

			
		</tr>
        <tr>
		    <td><span class="font"><span class='customStrong'>छात्र द्वारा स्वयं भरा गया है? (Is it filled by the student themselves?):</span> </span></td>
                		
<td><span class="font">&nbsp;{{@$yesno[@$master->is_self_filled]}}</span></td>
			<td><span class="font"><span class='customStrong'> एसएसओ (SSO):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->ssoid}}</span></td>
		</tr>

		<tr>
		   <td><span class="font"><span class='customStrong'>विभिन्न संकाय विषय(Multiple Faculty Subjects):</span> </span></td>
		     @if(!empty($master->application->is_multiple_faculty))
			 <td><span class="font">&nbsp;{{@$yesno[@$master->application->is_multiple_faculty]}}</span></td>
		     @else
			 <td><span class="font">&nbsp;N/A</span></td>
		     @endif
			<td><span class="font"><span class='customStrong'>मुख्य संकाय(Preferred Faculty):</span>&nbsp;</span></td>
			 @if(!empty($master->application->selected_faculty))
			<td><span class="font">&nbsp;{{@$master->application->selected_faculty}}</span></td>
		     @else
			 <td><span class="font">&nbsp;N/A</span></td>
		     @endif
			
			
		</tr>
		<tr>
		   <td><span class="font"><span class='customStrong'>धर्म(Religion):</span> </span></td>
			<td><span class="font">&nbsp;{{@$religion[@$master->application->religion_id]}}</span></td>
		
			<td><span class="font"><span class='customStrong'>राष्ट्रीयता (Nationality):</span>&nbsp;</span></td>
			<td><span class="font">&nbsp;{{@$nationality[@$master->application->nationality]}}</span></td>
			
		</tr>
		<tr>
		
		<td><span class="font"><span class='customStrong'>वंचित वर्ग(Disadvantage Group):</span></span></td>
			<td><span class="font">&nbsp;{{@$dis_adv_group[@$master->application->disadvantage_group]}}</span></td>
		
			
		
			<td><span class="font"><span class='customStrong'>दिव्यांगता(Disability):</span> </span></td>
			<td><span class="font">&nbsp;{{@$disability[@$master->application->disability]}}</span></td>
			</tr>
		<tr>
			
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>शहरी/ग्रामीण(Rural/Urban):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{@$rural_urban[@$master->application->rural_urban]}}</span></td>
			<td><span class="font"><span class='customStrong'>आधार नंबर (Aadhar Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->application->aadhar_number}}</span></td>
		</tr>
		<tr>
			
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>मोबाइल नंबर (Mobile Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->mobile}}</span></td>
			<td><span class="font"><span class='customStrong'>ईमेल (Email):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->email}}</span></td>
		</tr>
		  <td><span class="font"><span class='customStrong'>जन आधार संख्या </br>(Jan Aadhar Number)</span></span></td>
		  @if(!empty($master->application->jan_aadhar_number))
			<td><span class="font">&nbsp;{{@$master->application->jan_aadhar_number}}</span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif
			
			
			<td><span class="font"><span class='customStrong'>क्या आप राजस्थान के निवासी हैं?  </br>(Are You from Rajasthan?):</span> </span></td>
			<td><span class="font">&nbsp;{{@$are_you_from_rajasthan[@$master->are_you_from_rajasthan]}}</span></td>
		</tr>
		@if(@$master->is_dgs=='1')
		<tr>
		<td><span class="font"><span class='customStrong'>पिछड़े वर्ग के छात्र </br>(Disadvantage Group Student):</span> </span></td>
		<td><span class="font">&nbsp;{{@$yesno[@$master->is_dgs]}}</span></td>
		<td><span class="font"><span class='customStrong'>	उपयोगकर्ता नाम(UserName) </br>(UserName):</span> </span></td>
		<td><span class="font">&nbsp;{{@$master->username}}</span></td>
		</tr>
	@endif
		<tr>
		    @if(@$master->course == 12)
			<td><span class="font"><span class='customStrong'>10 वीं उत्तीर्ण होने का वर्ष </br>(10th Year of Passing):</span> </span></td>
			<td><span class="font">&nbsp;{{@$rsos_years[@$master->application->year_pass]}}</span></td>
			@endif
			<td><span class="font"><span class='customStrong'>पूर्व योग्यता(Previous Qualification):</span></span></td>
			<td><span class="font">&nbsp;{{@$pre_qualifi[@$master->application->pre_qualification]}}</span></td>
			
			 @if(@$master->course == 10)
			<td><span class="font"><span class='customStrong'>अध्ययन का माध्यम (Medium of Study):</span></span></td>
			<td><span class="font">&nbsp;{{@$midium[@$master->application->medium]}}</span></td>
			@endif
		</tr>
		 @if(@$master->course == 12)
		<tr>
			<td><span class="font"><span class='customStrong'>अध्ययन का माध्यम (Medium of Study):</span></span></td>
			<td><span class="font">&nbsp;{{@$midium[@$master->application->medium]}}</span></td>
		</tr>
		@endif
		<tr>
			<td><span class="font"><span class='customStrong'>क्या ओटीपी सत्यापित है?(Is OTP verified):</span></span></td>
			<td><span class="font">&nbsp;{{@$yesno[@$master->is_otp_verified]}}</span></td>
		</tr>
		
		@if($master->exam_month == 2)
		@if(!empty(@$changerequeststudentgetdate > 0))
		<tr>
			<td><span class="font"><span class='customStrong'>आपने परिवर्तन अनुरोधों के लिए कितनी बार आवेदन किया है?(How many times have you applied for change requests):</span></span></td>
			<td><span class="font">&nbsp;{{@$changerequeststudentgetdate}}</span></td>
		</tr>
		@endif
		@else
		@if(@$changerequeststudentgetdatecount > 0)
		<tr>
			<td><span class="font"><span class='customStrong'>आपने परिवर्तन अनुरोधों के लिए कितनी बार आवेदन किया है?(How many times have you applied for change requests):</span></span></td>
			<td><span class="font">&nbsp;{{@$changerequeststudentgetdatecount}}</span></td>
		</tr>
		@endif
		@endif
	
		
	</tbody>
</table>
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">बैंक विवरण( Bank Details)</a></span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;Account Holder Name	</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Branch Name &nbsp; </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Account Number</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;IFSC Code</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Bank Name </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Linked Mobile</span></span></td>
		</tr>
		<tr>
			<td class="font">&nbsp;{{@$master->bankdetils->account_holder_name	}}</td>
			<td class="font">&nbsp;{{@$master->bankdetils->branch_name}}</td>
			<td class="font">&nbsp;{{@$master->bankdetils->account_number}}</td>
			<td class="font">&nbsp;{{@$master->bankdetils->ifsc_code}}</td>
			<td class="font">&nbsp;{{@$master->bankdetils->bank_name}}</td>
			<td class="font">&nbsp;{{@$master->bankdetils->linked_mobile}}</td>
		</tr>
	</tbody>
</table>

	@if(@$isShowVerifcationPart)
		<div>
			<span class="fonthead"><span class='customStrong'><a style="font-size:14px;">सत्यापन विवरण</a>(Verification Details)</span></span>
		</div>
		<table border="1" style="width:100%;">
			<tbody>
				<tr> 
					@if(@$master->department_status != 1)
						<td><span class="font"><span class='customStrong'>सत्यापनकर्ता (Verifier):</span></span></td>
						<td><span class="font">&nbsp;{{@$fresh_student_verfication_status[@$master->verifier_status]}}</span></td> 
						<td><span class="font"><span class='customStrong'>विभाग (Department):</span></span></td>
						@if(@$master->ao_status !=1 && @$master->ao_status != ' ')
							<td><span class="font"><span class='customStrong'>शैक्षणिक अधिकारी(Academic Officer):</span></span></td>
						@endif	
						<td> 
						<span class="font">&nbsp;
						@if(@$studentamount && $studentamount > 0 && @$documentverifications->role_id == 58 && @$documentverifications->challan_tid == null && @$master->department_status)
							{{ "Clarification of payment pending at the student level." }} 
						@else
							{{@$fresh_student_verfication_status[@$master->department_status]}}
						@endif	
						</span></td>
						@if(@$master->ao_status !=1 && @$master->ao_status != ' ')
							<td><span class="font">&nbsp;{{@$fresh_student_verfication_status[@$master->ao_status]}}</span></td>
						@endif
						
					@else
						<td><span class="font"><span class='customStrong'>सत्यापनकर्ता (Verifier):</span></span></td>
						<td colspan="3"><span class="font">&nbsp;{{@$fresh_student_verfication_status[@$master->verifier_status]}}</span></td>
					@endif
				</tr>
			</tbody>
		</table>
	@endif


@php 
$adm_com_sub_cnt = 0;
$adm_add_sub_cnt = 0;
@endphp
@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
	@php if(@$values['is_additional']==0){ $adm_com_sub_cnt++; }  if(@$values['is_additional']==1){ $adm_add_sub_cnt++; }  @endphp
@endforeach

@if($adm_com_sub_cnt > 0)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">प्रवेश विषय विवरण(Admission Subject Details)</a></span></span></div>
<div><span class="fonthead"><span class='customStrong'>&nbsp;अनिवार्य विषय विवरण(Compulsory Subject Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class="font"><span class='customStrong'>Compulsory Subject</span></span></td>
		</tr>-->
		<tr>
			@for($i=1;$i <= $adm_com_sub_cnt;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		
		<tr>
			@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
			@if(@$values['is_additional']==0)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endif
			@endforeach
		</tr>
			
	</tbody>
</table>
@endif

@if($adm_add_sub_cnt > 0)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">अतिरिक्त विषय विवरण(Additional Subject Details)</a></span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class='customStrong'><span class="font">Additional Subject</span></span></td>
		</tr>-->
		
		<tr>
			@for($i=1;$i <= $adm_add_sub_cnt;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		
		<tr>
			@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
			@if(@$values['is_additional']==1)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endif
			@endforeach
		</tr>
	</tbody>
</table>
@endif
@if(!empty(@$master->tocdetils->student_id))
@if(@$master->adm_type != 5)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">टीओसी बोर्ड विवरण</a>(TOC Board Details) :</span></span></div>

			@php $fp = ''; 
			if($master->adm_type == 1 || $master->adm_type == 2){
			$fp = 'failing';
			}else{
			$fp = 'passing';
			}
			$streamval = 'stream'.$master->stream;
			$isyearof = 'is_year_of_'.$fp.'_required_'.$streamval;
			@endphp
			@php
			$fld = "is_year_of_passing_required_stream1";
			if(@$passfailyers->$fld){
			$lblName = "Falling";$fldYearPassFailName="year_fail";
			
			} 
			$fld = "is_year_of_passing_required_stream2";
			if(@$passfailyers->$fld){
			$lblName = "Falling";$fldYearPassFailName="year_fail";
			}
			$fld = "is_year_of_failing_required_stream1";
			if(@$passfailyers->$fld){
			$lblName = "Passing";$fldYearPassFailName="year_pass";
			}
			$fld = "is_year_of_failing_required_stream2";
			if(@$passfailyers->$fld){
			$lblName = "Passing";$fldYearPassFailName="year_pass";
			} 
            @endphp

<table border="1" style="width:100%;">
	<tbody>
		<tr>
		   @if(@$master->adm_type == 1 || @$master->adm_type == 2 || @$master->adm_type == 4 )
		    <!--<td><span class="font"><span class='customStrong'>&nbsp;Whether you are applying for Transfer of Credit</span></span></td> -->
			<td><span class="font"><span class='customStrong'>&nbsp;Name Of Board</span></span></td>
		
			<td><span class="font"><span class='customStrong'>&nbsp;Years of <?php echo @$lblName; ?></span></span></td> 
		    <td><span class="font"><span class='customStrong'>&nbsp;Roll No.</span></span></td>
			@endif

		</tr>
		<tr> 
		 @if(@$master->adm_type == 1 || @$master->adm_type == 2 || @$master->adm_type == 4 )
		    <!--<td class="font">&nbsp;{{@$yesno[@$master->application->toc]}}</td>-->
		 @endif
			<td class="font">&nbsp;{{@$getBoardList[@$master->tocdetils->board]}}</td>
			
			@if(!empty($master->tocdetils->year_fail))
			<td class="font">&nbsp;{{@$tocpassfail[@$master->tocdetils->year_fail]}}</td>
			@else
			<td class="font">&nbsp;{{@$tocpassyear[@$master->tocdetils->year_pass]}}</td>
			@endif
		    <td class="font">&nbsp;{{@$master->tocdetils->roll_no}}</td>
		</tr>
	</tbody>
</table>
@endif

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">टीओसी विषय विवरण</a>( TOC Subjects Details):</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class="font"><span class='customStrong'>क्रेडिट&nbsp;स्थानांतरण(Transfer of Credit) : 
			@if(@$master->application->toc==1) Yes  @else  No @endif </span></span></td>
		</tr>-->
		@if(!empty($master_subject_details['tocSubjectDetails']))
		<tr>
    <td style="width:20%;">Subjects Name(Code)</td>
    <td style="width:20%;">Theory Marks</td>
    <td style="width:20%;">Practical Marks</td>
    <td style="width:20%;">Total Marks</td>
   </tr>
   @foreach(@$master_subject_details['tocSubjectDetails'] as $key => $values)
   <tr>
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			<td><span class="font">&nbsp;{{ @$values['theory'] }}</span></td>
			<td><span class="font">&nbsp;{{ @$values['practical'] }}</span></td>
			<td><span class="font">&nbsp;{{ @$values['total_marks'] }}</span></td>
			</tr>
			@endforeach
		@endif
	</tbody>
</table>
@endif
@if(!empty($master_subject_details['examSubjectDetails']))
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">परीक्षा विषय विवरण</a>(Examination Subject Details )</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="7"><span class="font"><span class='customStrong'>Examination Subject in which the candidate wants to appear</span></span></td>
		</tr>-->
		<tr>
			@php $exam_subject_count = count($master_subject_details['examSubjectDetails']); @endphp
			@for($i=1;$i <= $exam_subject_count;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		<tr>
		    @foreach(@$master_subject_details['examSubjectDetails'] as $key => $values)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endforeach
		</tr>
	</tbody>
</table>
@endif
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">परीक्षा शुल्क विवरण</a>(Exam Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;Registration Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Additional Subject Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Forward Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;TOC Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Practical Fees </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Exam Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Late Fees &nbsp; </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Total Fees</span></span></td>
		</tr>
		<tr>
			<td class="font">&nbsp;{{@$master->studentfees->registration_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->add_sub_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->forward_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->toc_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->practical_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->readm_exam_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->late_fee}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->total}}</td>
		</tr>
	</tbody>
</table>
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">स्थायी पते का विवरण</style></a>(Permanent Address Details)
@if(@$master->address->is_both_same == 1)
	<span>पत्राचार का पता स्थायी पते के समान है?(Correspondence address is identical to permanent address?) &nbsp;{{@$yesno[@$master->address->is_both_same]}}</span>
@endif 
</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
            <td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;पता(Address):</span></span></td>
			<td style="width: 35%;">&nbsp;{{@$master->address->address1}}</td>
			<td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;राज्य(State):</span></span></td>
			<td style="width: 35%;">&nbsp;{{@$master->address->state_name}}</td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;ज़िला(District):</span></span></td>
			<td>&nbsp;{{@$master->address->district_name}}</td>
			<td><span class="font"><span class='customStrong'>&nbsp;ब्लॉक/तहसील(Block/Tehsil):</span></span></td>
			<td>&nbsp;{{@$master->address->block_name}}/{{@$master->address->tehsil_name}}</td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;शहर/गाँव(City/Village):</span></span></td>
			<td>&nbsp;{{@$master->address->city_name}}</td>
			<td><span class="font"><span class='customStrong'>&nbsp;पिन कोड(Pincode)</span></span></td>
			<td>&nbsp;{{@$master->address->pincode}}</td>
		</tr>
	</tbody>
</table>

@if(@$master->address->is_both_same != 1)
	<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पत्राचार पते का विवरण</style></a>(Correspondence Address Details)</span></span></div>

	<table border="1" style="width:100%;">
		<tbody>
		   @if(@$master->address->is_both_same == 1)
			  <!-- <tr>
					<td style="width: 35%;"><span class="font"><span class='customStrong'>&nbsp;पत्राचार के समान(Same as Correspondence):</span></span></td>
					<td style="width: 35%;">&nbsp;{{@$yesno[@$master->address->is_both_same]}}</td>
				</tr> -->
		  @else
				<tr>
					<td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;पता(Address):</span></span></td>
					<td style="width: 35%;">&nbsp;{{@$master->address->current_address1}}</td>
					<td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;राज्य(State):</span></span></td>
					<td style="width: 35%;">&nbsp;{{@$master->address->current_state_name}}</td>
				</tr>
				<tr>
					<td><span class="font"><span class='customStrong'>&nbsp;ज़िला(District):</span></span></td>
					<td>&nbsp;{{@$master->address->current_district_name}}</td>
					<td><span class="font"><span class='customStrong'>&nbsp;ब्लॉक/तहसील(Block/Tehsil):</span></span></td>
					<td>&nbsp;{{@$master->address->current_block_name}}/{{@$master->address->current_tehsil_name}}</td>
				</tr>
				<tr>
					<td><span class="font"><span class='customStrong'>&nbsp;शहर/गाँव(City/Village):</span></span></td>
					<td>&nbsp;{{@$master->address->current_city_name}}</td>
					<td><span class="font"><span class='customStrong'>&nbsp;पिन कोड(Pincode)</span></span></td>
					<td>&nbsp;{{@$master->address->current_pincode}}</td>
				</tr>
				
			@endif
		</tbody>
	</table>
@endif



	  @if($master->application->category_a !=7)
		@if(@$master->exam_month == 2 && $examyear == 126)
		@if(!empty($changerequeststudentgetdate > 0))
		<div>
			<span class="fonthead">
				<span class='customStrong'>
					<a style="font-size:14px;">ट्रांजैक्शन विवरण</a>(Transaction Details Change Request)
				</span>
			</span>
		</div> 

		<table border="1" style="width:100%;">
			<tbody>
			@foreach ($changerequeststudentgetdatachangerequests as $user)
				<tr>
					<td style="width: 25%;"><span class='customStrong'>
						<span class="font">&nbsp;Payment Date</span></span>
					</td>
					<td>{{@date("d-m-Y h:i:s a",strtotime(@$user->updated_at))}}</td>
					<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
					<td>{{@$user->challan_tid}}</td>
					<td><span class='customStrong'><span class="font">&nbsp;Amount</span></span></td>
					<td>{{@$user->amount}}</td>
				</tr>
		    @endforeach  
			</tbody>
		</table>
		@endif
		@else
		@if(!empty(@$changerequeststudentgetdatecount > 0))
		<div>
			<span class="fonthead">
				<span class='customStrong'>
					<a style="font-size:14px;">ट्रांजैक्शन विवरण</a>(Transaction Details Change Request)
				</span>
			</span>
		</div> 

		<table border="1" style="width:100%;">
			<tbody>
			@foreach ($changerequeststudentcount as $user)
				<tr>
				   @if(@$user->change_request_status == 'YES')
					<td style="width: 25%;"><span class='customStrong'>
						<span class="font">&nbsp;Payment Date</span></span>
					</td>
					<td>{{@date("d-m-Y h:i:s a",strtotime(@$user->created_at))}}</td>
					<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
					<td>{{@$user->challan_tid}}</td>
					<td><span class='customStrong'><span class="font">&nbsp;Amount</span></span></td>
					<td>{{@$user->amount}}</td>
					 @elseif(@$user->change_request_status == 'NO')
					 <td><span class='customStrong'><span class="font">&nbsp;Change Request Difference</span></span></td>
					 <td>{{0}}</td>
					 @endif
				</tr>
		    @endforeach  
			</tbody>
		</table>
		@endif
		@endif 
		
		
		@if(!empty(@$master->challan_tid) && !empty (@$master->submitted ))
		<div>
			<span class="fonthead">
				<span class='customStrong'>
					<a style="font-size:14px;">ट्रांजैक्शन विवरण</a>(Transaction Details)
				</span>
			</span>
		</div> 

		<table border="1" style="width:100%;">
			<tbody>
				<tr>
					<td style="width: 25%;"><span class='customStrong'>
						<span class="font">&nbsp;Payment Date</span></span>
					</td>
					<td>{{@date("d-m-Y h:i:s a",@strtotime(@$master->submitted))}}</td>
					<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
					<td>{{@$master->challan_tid}}</td>
				</tr>
			</tbody>
		</table>
@endif 
@endif 

 
@if(!empty(@$documentverifications->challan_tid) && !empty (@$documentverifications->submitted ))
		<div>
			<span class="fonthead">
				<span class='customStrong'>
					<a style="font-size:14px;">स्पष्टीकरण ट्रांजैक्शन विवरण</a>(Clarification Transaction Details)
				</span>
			</span>
		</div>
		<table border="1" style="width:100%;">
			<tbody>
				<tr>
					<td style="width: 15%;"><span class='customStrong'>
						<span class="font">&nbsp;Payment Date</span></span>
					</td>
					<td style="width:15%">{{@date("d-m-Y h:i:s a",@strtotime(@$documentverifications->submitted))}}</td>
					<td style="width:15%"><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
					<td style="width:15%">{{@$documentverifications->challan_tid}}</td>
					<td style="width:15%"><span class='customStrong'><span class="font">&nbsp;Amount</span></span></td>
					<td style="width:15%">{{@$documentverifications->fee_paid_amount}}</td>
				</tr>
			</tbody>
		</table>
@endif 

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">लॉक एवं सब्मिट विवरण</a>(Lock and Submit Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 25%;"><span class='customStrong'><span class="font">Date</span></span></td>
			<td>&nbsp;{{@date("d-m-Y h:i:s a",@strtotime(@$master->application->locksubmitted_date))}}</td>
			<td><span class='customStrong'><span class="font">&nbsp; Is Lock and submit</span></span></td>
			@if(@$master->application->locksumbitted == 1)
			<td>&nbsp; Yes</td>
		    @else
			<td>&nbsp;No</td>
			@endif
		</tr>
	</tbody>
</table>
<div><span class="fonthead"><span class='customStrong'>घोषणा(Decleration)</span></span>
			
@if(@$master->challan_tid == '' && @$master->submitted != '')
	@if(@$master->application->category_a !=7 )
	@else if(@$master->studentfees->total != 0)
   
						<span style="color: red;font-size:14px;">
                                           (to pay application form fee as received payment link on mobile.):-<a  href="<?php echo $url; ?>">&nbsp;&nbsp;Fees Pay</a>
                                        </span>
						@endif
@endif  			&nbsp;
</div>
				<div>
					<ul style="margin-top:-5px;">
						<li>
							<span style="font-size:12px;font-family: Hind;">
								@php echo 	Config::get('global.fresh_form_undertaking_msg_student_pdf'); @endphp
							</span>
						</li>
						<li> 
							<span style="font-size:12px;font-family: Hind;">
								@php echo Config::get('global.fresh_form_undertaking_msg'); @endphp
							</span>
						</li>
					</ul>
				</div> 
				
				@if(@$master->is_eligible ==1) 
					<!-- eligible --> 
					<div class="right customStrong" style="font-size:18px;text-align:center;">
						<span>
						<img alt="RSOS" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->signature)}}" width="50px" /><br>
						<b>Signature</b>
						</span>
					</div>
				@endif
						
						
				
					<style>
						ul {
							list-style: none;
							margin-left: -30px;
						}

						ul li:before {
							content: '✓';
						}
					</style>
</body>
@endsection
@section('customjs')

@endsection