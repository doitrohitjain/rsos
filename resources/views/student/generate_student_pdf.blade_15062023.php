<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 

<style type="text/css">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
table, th, td{
  border: 1px solid black;
  border-collapse: collapse;
  font-size:14px;
  
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
  /* font-family: Arial;  */
 }
</style>
</head>
<body style="margin-top:-6%,margin-bottom:0%">
<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 40px; border-radius: 10px" alt="image upload button"/>
  </div>  
RSOS Form - Academic session {{ @$curent_session_text }}
  
</div>&nbsp;
@if($master->application->category_a !=7)
<span style="color: red; font-size:20px;">Notes:- Forwarding Fees of Rs. 50/- and Online Service Fees of Rs. 30/- to be paid offline to the AI center</span></br>&nbsp;
@endif
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td colspan="3"><span style="font-size:13px;"><span class=''>&nbsp;Enrollment Number:</span> <b>{{@$master->enrollment}} </b></span></td>
		</tr>
		<tr>
			<td colspan="3"><span class="font"><span class=''>&nbsp;AI Center Name:</span> {{@$aiCenters[@$master->ai_code]}}</span></td>
		</tr>
		<tr>
			<td style="width: 50%;"><span class="font"><span class='customStrong'>&nbsp;प्रवेश आवेदन का प्रकार(Admission of Type)</span></span></td>
			<td colspan="2" rowspan="1"><span style="font-size:12px;">&nbsp;{{@$adm_types[@$master->adm_type]}}</span></td>
		</tr>
		<tr>
			<td style="width: 50%;"><span class="font"><span class='' style="">&nbsp;परीक्षा&nbsp;(Exam)</span></span></td>
			<td colspan="2"><span class="font">&nbsp;{{@$exam_session[@$master->exam_month]}}</span></td>
		</tr>
	</tbody>
</table>

<div><span class="font" style="font-family: Noto Sans,sans-serif;"><span class='customStrong'>&nbsp;व्यक्तिगत विवरण(Personal Details)</span></span></div>

<table border="1" style="width:100%;">
		<tr>
			<td style="width: 25%;"><span class="font"><span class='customStrong'>&nbsp;फोटो (Photograph)</span> </span></td>
			<td style="width: 25%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.$master->document->photograph)}}" width="50px" /></span></td>
			<td style="width: 25%;"><span class="font"><span class='customStrong'>&nbsp;हस्ताक्षर(Signature)</span></span></td>
			<td style="width: 25%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.$master->document->signature)}}" width="50px" /></span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;आवेदक का नाम (Applicant&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->name}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;पिता का नाम (Father&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->father_name}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;माँ का नाम (Mother&#39;s Name): </span></span></td>
			<td><span class="font">&nbsp;{{@$master->mother_name}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;लिंग( Gender):</span> </span></td>
			<td><span class="font">&nbsp;{{@$gender_id[@$master->gender_id]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;जन्म तिथि (Date of Birth) (DD-MM-YYYY):</span></span></td>
			<td><span class="font">&nbsp;{{ date('d-m-Y', strtotime(@$master->dob))}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;धर्म(Religion):</span> </span></td>
			<td><span class="font">&nbsp;{{@$religion[@$master->application->religion_id]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;राष्ट्रीयता (Nationality):</span>&nbsp;</span></td>
			<td><span class="font">&nbsp;{{@$nationality[@$master->application->nationality]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;श्रेणी&nbsp;ए (Category A):</span> </span></td>
			<td><span class="font">&nbsp;{{@$categorya[@$master->application->category_a]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;दिव्यांगता(Disability):</span> </span></td>
			<td><span class="font">&nbsp;{{@$disability[@$master->application->disability]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;अध्ययन का माध्यम(Medium of Study):</span> </span></td>
			<td><span class="font">&nbsp;{{@$midium[@$master->application->medium]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;पाठ्यक्रम(Course):</span> </span></td>
			<td><span class="font">&nbsp;{{@$course[@$master->course]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;वंचित वर्ग(Disadvantage Group):</span></span></td>
			<td><span class="font">&nbsp;{{@$dis_adv_group[@$master->application->disadvantage_group]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;शहरी/ग्रामीण(Rural/Urban):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{@$rural_urban[@$master->application->rural_urban]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;आधार नंबर (Aadhar Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->application->aadhar_number}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp; स्ट्रीम(Stream):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{$stream_id[@$master->stream]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;क्या आप राजस्थान हैं और नहीं (Are You Rajasthan and Not):</span> </span></td>
			<td><span class="font">&nbsp;{{$are_you_from_rajasthan[@$master->are_you_from_rajasthan]}}</span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;मोबाइल नंबर (Mobile Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->mobile}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;ईमेल (Email):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->email}}</span></td>
		</tr>
		  <td><span class="font"><span class='customStrong'>&nbsp;जन आधार संख्या&nbsp;(Jan Aadhar Number)</span></span></td>
		  @if(!empty($master->application->jan_aadhar_number))
			<td><span class="font">&nbsp;{{@$master->application->jan_aadhar_number}}</span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif
		</tr>
	
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;10 वीं उत्तीर्ण होने का &nbsp;वर्ष(10th &nbsp; Year of Passing):</span> </span></td>
			<td><span class="font">&nbsp;{{@$rsos_years[@$master->application->year_pass]}}</span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;पूर्व योग्यता(Previous &nbsp; Qualification):</span></span></td>
			<td><span class="font">&nbsp;{{@$pre_qualifi[@$master->application->pre_qualification]}}</span></td>
		</tr>
		
		
	
		
	</tbody>
</table>
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;बैंक विवरण( BANK Details)</span></span></div>
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
@php 
$adm_com_sub_cnt = 0;
$adm_add_sub_cnt = 0;
@endphp
@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
	@php if(@$values['is_additional']==0){ $adm_com_sub_cnt++; }  if(@$values['is_additional']==1){ $adm_add_sub_cnt++; }  @endphp
@endforeach

@if($adm_com_sub_cnt > 0)
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;विषय विवरण(Subject Details)</span></span></div>
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;अनिवार्य विषय(Compulsory Subject)</span></span></div>
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
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;अतिरिक्त विषय(Additional Subject)</span></span></div>
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
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;क्रेडिट&nbsp;स्थानांतरण(Transfer of Credit) :</span></span></div>

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
		    <td><span class="font"><span class='customStrong'>&nbsp;Whether you are applying for Transfer of Credit</span></span></td>
		   @endif
			<td><span class="font"><span class='customStrong'>&nbsp;Name Of Board</span></span></td>
		
			<td><span class="font"><span class='customStrong'>&nbsp;Years of <?php echo @$lblName; ?></span></span></td> 
		    <td><span class="font"><span class='customStrong'>&nbsp;Roll No.</span></span></td>
			
		</tr>
		<tr> 
		 @if(@$master->adm_type == 1 || @$master->adm_type == 2 || @$master->adm_type == 4 )
		    <td class="font">&nbsp;{{@$master->application->toc}}</td>
		 @endif
			<td class="font">&nbsp;{{@$getBoardList[@$master->tocdetils->board]}}</td>
		     
			<td class="font">&nbsp; 
			{{@$master->tocdetils->$fldYearPassFailName}}</td>
		    <td class="font">&nbsp;{{@$master->tocdetils->roll_no}}</td>
		</tr>
	</tbody>
</table>
@endif

<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;टीओसी&nbsp;विषय(Toc subjects) :</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class="font"><span class='customStrong'>क्रेडिट&nbsp;स्थानांतरण(Transfer of Credit) : 
			@if(@$master->application->toc==1) Yes  @else  No @endif </span></span></td>
		</tr>-->
		@if(!empty($master_subject_details['tocSubjectDetails']))
		<tr>
    <th style="width:20%;">Subjects Name(Code)</th>
    <th style="width:20%;">Theory Marks</th>
    <th style="width:20%;">Practical Marks</th>
    <th style="width:20%;">Total Marks</th>
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
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;Examination Subject in which the candidate wants to appear : </span></span></div>
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
<div><span class="font" style="font-family: Hind;"><span class='customStrong'>&nbsp;परीक्षा शुल्क विवरण(Exam Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;Registration Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Additional Subject Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;TOC Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Practical Fees </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Exam Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Late Fees &nbsp; </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Total Fees</span></span></td>
		</tr>
		<tr>
			<td class="font">&nbsp;{{@$master->studentfees->registration_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->add_sub_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->toc_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->practical_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->readm_exam_fees}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->late_fee}}</td>
			<td class="font">&nbsp;{{@$master->studentfees->total}}</td>
		</tr>
	</tbody>
</table>
<div><span style="font-size:12px;font-family: Hind;"><span class='customStrong'>&nbsp;पते का विवरण(Address Details)</span></span></div>

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
			<td><span class="font"><span class='customStrong'>&nbsp;पिन कोड(Pincode</span></span></td>
			<td>&nbsp;{{@$master->address->pincode}}</td>
		</tr>
	</tbody>
	</table><br>
	  @if($master->application->category_a !=7)
		@if(!empty(@$master->challan_tid) && !empty (@$master->submitted ))
		<div><span style="font-size:12px;"><span class='customStrong'>&nbsp;Transaction Details</span></span></div>

		<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Payment Date</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
		</tr>
		<tr>
			<td>{{@$master->submitted}}</td>
			<td>{{@$master->challan_tid}}</td>
		</tr>
	</tbody>
</table>
@endif 
@endif 

<div><span style="font-size:12px;"><span class='customStrong'>&nbsp;Lock and submit Details</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Lock and submit Date Time</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp; Is Lock and submit</span></span></td>
		</tr>
		<tr>
			<td>&nbsp;{{@$master->application->locksubmitted_date}}</td>
			@if(@$master->application->locksumbitted == 1)
			<td>&nbsp; Yes</td>
		    @else
			<td>&nbsp;No</td>
			@endif
			
		</tr>
	</tbody>
</table>

			
@if(@$master->challan_tid == '' && @$master->submitted =='')
	@if(@$master->application->category_a !=7 )
	@else if(@$master->studentfees->total != 0)
   
						<span style="color: red;font-size:14px;">
                                           (to pay application form fee as received payment link on mobile.):-<a  href="<?php echo $url; ?>">&nbsp;&nbsp;Fees Pay</a>
                                        </span>
						@endif
@endif  						
					<p class="mb-1">
					<label>
					<input type="checkbox" name="" value="" checked  disabled>
					<span style="font-size:14px;font-family: Hind;">
					प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्ठियाँ सही भरी गई है। मेरे द्वारा दी गई सभी प्रविष्टियों में कोई त्रुटि पाऐ जाने पर आवेदन रद्द होने का उत्तरदायित्व मेरा रहेगा।</span>
					</label>
					</p><br>
					
					<input type="checkbox" name="" value="" checked disabled>
					<span style="font-size:14px;font-family: Hind;">
					प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्टियों की जाँच सावधानीपूर्वक संलग्न प्रमाण-पत्रों द्वारा कर ली गई है तथा समस्त प्रविष्टियां सही पाई गई एवं अभ्यर्थी आवेदित श्रेणी तथा पाठ्यक्रम के लिए आर.एस.ओ.एस. के नियमानुसार पात्र है। आवेदन पत्र मय दस्तावेज सत्यापित कर आर.एस.ओ.एस. कार्यालय भिजवाये जा रहे है।</span>
					</label>
					
					</body>
</html>


 



