
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
</style>
</head>
<body style="margin-top:-10%,margin-bottom:0%">
<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 40px; border-radius: 10px" alt="image upload button"/>
  </div>  

RSOS Reval Form - Academic Session {{@$exam_month[@$reval_exam_month]}} {{@$admission_sessions[@$reval_exam_year]}}
  
</div>&nbsp;
<table border="1" style="width:100%;">
	<tbody>

		<tr>
			<td colspan="3">
			
			<span style="font-size:13px;"><b>Enrollment Number:</b></span>
            <span class="font">{{@$master->enrollment}}</span></td>
		</tr>
		<tr>
			<td colspan="3"><span style="font-size:13px;"><span><b>AI Center Name:</b></span> <span class="font">{{@$aiCenters[@$master->ai_code]}}</span></td>
		</tr>
		<tr>
			<td style="width: 50%;">
			<span style="font-size:13px;">
			<span class='customStrong'>
			<b>प्रवेश आवेदन का प्रकार(Admission Type)
			</b>
			</span>
			</span>
			</td>
			
			<td colspan="2" rowspan="1">
			<span class="font" >{{@$adm_types[@$master->adm_type]}}</span></td>
		</tr>
		<tr>
			<td style="width: 50%;">
			<span style="font-size:13px;"><b>परीक्षा(Exam)</b></span></td>
			<td colspan="2"><span class="font">{{@$exam_month[@$master->exam_month]}} {{@$admission_sessions[@$master->exam_year]}}</span></td>
		</tr>
	</tbody>
</table>

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">व्यक्तिगत विवरण(Personal Details)</a></span></span></div>

<table border="1" style="width:100%;">
		<tr>
			<td style="width: 25%;"><span class="font"><span class='customStrong'>फोटो (Photograph)</span> </span></td>
			<td style="width: 25%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->photograph)}}" width="50px" /></span></td>
			<td style="width: 25%;"><span class="font"><span class='customStrong'>हस्ताक्षर(Signature)</span></span></td>
			<td style="width: 25%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->signature)}}" width="50px" /></span></td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>आवेदक का नाम (Applicant&#39;s Name):</span></span></td>
			<td><span class="font">&nbsp;{{@$master->name}}</span></td>
			
			<td><span class="font"><span class='customStrong'>जन्म तिथि (Date of Birth)  </br>(DD-MM-YYYY):</span></span></td>
			<td><span class="font">&nbsp;{{ date('d-m-Y', strtotime(@$master->dob))}}</span></td>
			
			
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
		
			<td><span class="font"><span class='customStrong'> एसएसओ (SSO):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->ssoid}}</span></td>
			
			
		</tr>
		<tr>
		    <td><span class="font"><span class='customStrong'>पाठ्यक्रम(Course):</span> </span></td>
			<td><span class="font">&nbsp;{{@$course[@$master->course]}}</span></td>
			<td><span class="font"><span class='customStrong'> स्ट्रीम(Stream):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{$stream_id[@$master->stream]}}</span></td>

			
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
	 
		<td><span class="font" width="50%"><span class='customStrong'>पुनर्मूल्यांकन आवेदन का प्रकार </br>(Reval Application Type)</span></span></td>
		  @if(!empty($master->reval_students->reval_type))
			<td><span class="font">&nbsp;<b>{{ $reval_types[@$master->reval_students->reval_type]}}</b></span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif 
		</tr>
		
		
	
		
	</tbody>
</table> 


@php 
$adm_com_sub_cnt = 0;
$adm_add_sub_cnt = 0;
@endphp
 

@if($adm_com_sub_cnt > 0)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">विषय विवरण(Reval Subjects	Details)</a></span></span></div>
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

<div style="display:none;"><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">टीओसी विषय विवरण</a>( TOC Subjects Details):</span></span></div>
<table border="1" style="width:100%;display:none;">
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

@if(@$master->RevalStudentSubject && !empty($master->RevalStudentSubject))
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पुनर्मूल्यांकन विषय विवरण</a>(Reval Subject Details )</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="7"><span class="font"><span class='customStrong'>Examination Subject in which the candidate wants to appear</span></span></td>
		</tr>-->
		<tr>
			@php $exam_subject_count = count($master->RevalStudentSubject); @endphp
			@for($i=1;$i <= $exam_subject_count;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		<tr>
		    @foreach(@$master->RevalStudentSubject as $key => $values)
			<td><span class="font">&nbsp;{{ @$subject_list[@$values['subject_id']] }}</span></td>
			@endforeach
		</tr>
	</tbody>
</table>
@endif
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">शुल्क विवरण</a>(Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			@if(@$master->RevalStudent->late_fee && $master->RevalStudent->late_fee > 0)
				<td><span class="font"><span class='customStrong'>&nbsp;Late Fees &nbsp; </span></span></td>
			@endif
			<td><span class="font"><span class='customStrong'>&nbsp;Total Fees</span></span></td>
		</tr>
		<tr>
			@if(@$master->RevalStudent->late_fee && $master->RevalStudent->late_fee > 0)
				<td class="font">&nbsp;{{@$master->RevalStudent->late_fees}}</td>
			@endif
			<td class="font">&nbsp;{{@$master->RevalStudent->total_fees}}</td>
		</tr>
	</tbody>
</table>
<div style="display:none;">
	<span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पते का विवरण</style></a>(Address Details)</span></span>
</div>
<table border="1" style="width:100%;display:none;">
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

<div style="display:none;"><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पत्राचार पते का विवरण</style></a>(Correspondence Address Details)</span></span></div>

<table border="1" style="width:100%; display:none;">
	<tbody>
       @if(@$master->address->is_both_same == 1)
		   <tr>
				<td style="width: 35%;"><span class="font"><span class='customStrong'>&nbsp;पत्राचार के समान(Same as Correspondence):</span></span></td>
				<td style="width: 35%;">&nbsp;{{@$yesno[@$master->address->is_both_same]}}</td>
			</tr>
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




	   
		@if(!empty(@$master->RevalStudent->challan_tid) && !empty (@$master->RevalStudent->submitted ))
		<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">ट्रांजैक्शन विवरण</a>(Transaction Details)</span></span></div>

		<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Payment Date</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
		</tr>
		<tr>
			
			<td>{{@date("d-m-Y h:i:sa",@strtotime(@$master->RevalStudent->submitted))}}</td>
			<td>{{@$master->RevalStudent->challan_tid}}</td>
		</tr>
	</tbody>
</table>
 
@endif 

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">लॉक एवं सब्मिट विवरण</a>(Lock and Submit Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">Date</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp; Is Lock and submit</span></span></td>
		</tr>
		<tr>
			<td>&nbsp;{{@date("d-m-Y h:i:sa",@strtotime(@$master->RevalStudent->locksubmitted_date))}}</td>
			@if(@$master->RevalStudent->locksumbitted == 1)
			<td>&nbsp; Yes</td>
		    @else
			<td>&nbsp;No</td>
			@endif
			
		</tr>
	</tbody>
</table>
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">घोषणा </a>(Decleration)</span></span> &nbsp;
</div>
				<table style="width:100%;">
					<tr>
						<td>
							<ul>
								<li>
									
									<span style="font-size:14px;font-family: Hind;">
										प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्ठियाँ सही भरी गई है। मेरे द्वारा दी गई सभी प्रविष्टियों में कोई त्रुटि पाऐ जाने पर आवेदन रद्द होने का उत्तरदायित्व मेरा रहेगा।
									</span>
								</li>
								<li>
									
									<span style="font-size:14px;font-family: Hind;">
										प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्टियों की जाँच सावधानीपूर्वक संलग्न प्रमाण-पत्रों द्वारा कर ली गई है तथा समस्त प्रविष्टियां सही पाई गई एवं अभ्यर्थी आवेदित श्रेणी तथा पाठ्यक्रम के लिए आर.एस.ओ.एस. के नियमानुसार पात्र है। आवेदन पत्र मय दस्तावेज सत्यापित कर आर.एस.ओ.एस. कार्यालय भिजवाये जा रहे है।
									</span>
								</li>
							</ul>
						</td>
					</tr>
				</table>			
					 
					
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