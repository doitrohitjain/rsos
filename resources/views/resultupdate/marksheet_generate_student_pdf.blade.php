
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
  font-size:18px;
  font-family:Arial;
  
}
 .cc51 {
  font-size: 30px;
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}
.font{
 font-size:15px;
 font-family:Arial;
 font-weight:normal;  
 }
 .fonthead{
 font-size:13px;
 font-family:Arial;
 font-weight:bold;  
 }
</style>
</head>
<body style="margin-top:-20%,margin-bottom:0%">
<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 50px; border-radius: 10px" alt="image upload button"/>
  </div> 
RSOS Revised/Duplicate Marksheet/Migration Form
</div>&nbsp;
<table border="1" style="width:100%;">
	<tbody>

		<tr>
			<td colspan="3">
			
			<span style="font-size:14px;"><b>Enrollment Number:</b></span>
            <span class="font">{{@$master->enrollment}}</span></td>
		</tr>
		<tr>
			<td colspan="3"><span style="font-size:14px;"><span><b>AI Center Name:</b></span> <span class="font">{{@$aiCenters[@$master->ai_code]}}</span></td>
		</tr>
		<tr>
			<td style="width: 50%;">
			<span style="font-size:14px;">
			<span class='customStrong'>
			<b>प्रवेश आवेदन का प्रकार(Admission Type)
			</b>
			</span>
			</span>
			</td>
			
			<td colspan="2" rowspan="1">
			<span class="font" >{{@$adm_types[@$master->adm_type]}}</span></td>
		</tr>
		<!--<tr>
			<td style="width: 50%;">
			<span style="font-size:13px;"><b>परीक्षा(Exam)</b></span></td>
			<td colspan="2"><span class="font">{{@$exam_month[@$master->exam_month]}} {{@$admission_sessions[@$master->exam_year]}}</span></td>
		</tr>-->
	</tbody>
</table>
<br>
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">व्यक्तिगत विवरण(Personal Details)</a></span></span></div>

<table border="1" style="width:100%;">
		<tr>
			<td style="width: 30%;"><span class="font"><span class='customStrong'>फोटो (Photograph)</span> </span></td>
			<td style="width: 30%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->photograph)}}" width="50px" /></span></td>
			<td style="width: 30%;"><span class="font"><span class='customStrong'>हस्ताक्षर(Signature)</span></span></td>
			<td style="width: 30%;"><span class="font"><img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.@$master->document->signature)}}" width="50px" /></span></td>
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
		<!--<tr>
			<td><span class="font"><span class='customStrong'>लिंग(Gender):</span> </span></td>
			<td><span class="font">&nbsp;{{@$gender_id[@$master->gender_id]}}</span></td>
		
			<td><span class="font"><span class='customStrong'> एसएसओ (SSO):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->ssoid}}</span></td>
			
			
		</tr>-->
		<!--<tr>
		    <td><span class="font"><span class='customStrong'>पाठ्यक्रम(Course):</span> </span></td>
			<td><span class="font">&nbsp;{{@$course[@$master->course]}}</span></td>
			<td><span class="font"><span class='customStrong'> स्ट्रीम(Stream):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{$stream_id[@$master->stream]}}</span></td>

			
		</tr>   -->
		<!--<tr>
			<td><span class="font"><span class='customStrong'>शहरी/ग्रामीण(Rural/Urban):</span> </span></td>
			  
			<td><span class="font">&nbsp;{{@$rural_urban[@$master->application->rural_urban]}}</span></td>
			<td><span class="font"><span class='customStrong'>आधार नंबर (Aadhar Number):</span> </span></td>
			<td><span class="font">&nbsp;{{@$master->application->aadhar_number}}</span></td>
		</tr>-->
		<tr>
			
		</tr>
		<!--<tr>
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
		</tr>-->
	 
		<td><span class="font" width="50%"><span class='customStrong'>मार्कशीट आवेदन प्रकार </br>(Marksheet  Application Type)</span></span></td>
		  @if(!empty($master->MarksheetStudent->marksheet_type))
			<td><span class="font">&nbsp;<b>{{ @$marsheet_type[@$master->MarksheetStudent->marksheet_type]}}</b></span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif 
			<td><span class="font" width="50%"><span class='customStrong'>
मार्कशीट दस्तावेज़ प्रकार </br>(Document Type)</span></span></td>
		  @if(!empty($master->MarksheetStudent->document_type))
			<td><span class="font">&nbsp;<b>{{ @$document_type[@$master->MarksheetStudent->document_type]}}</b></span></td>
			@else
			<td><span class="font">&nbsp;N/A</span></td>
			@endif 
			
		</tr>
		
		
	
		
	</tbody>
</table> 
<br>
@if(count($master->ReviserdStudentData) != 0) 
<div><span class="fonthead"><span class='customStrong'><a style="font-size:15px;">सुधार विवरण</a>(Correction Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Correction Fields</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp;Correction Value</span></span></td></tr>
		@foreach($master->ReviserdStudentData as $data)
		<tr>
			<td>&nbsp; {{ucwords(str_replace( array( '\'', '"',
      ',' , ';', '<','_','>' ), ' ', @$data->correction_field))}}</td>
	  @if(@$data->correction_field == 'dob')
		<td>{{date("d-m-Y",strtotime($data->incorrect_value))}}</td>
		@else
			<td>{{$data->incorrect_value}}</td>
		@endif
		</tr>
		@endforeach
	</tbody>
</table>
@endif
<br>
<div><span class="fonthead"><span class='customStrong'><a style="font-size:15px;">शुल्क विवरण</a>(Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
		<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Total Fees</span></span></td>
			<td><span class='customStrong'><span class="">&nbsp;{{$master->MarksheetStudent->total_fees}}</span></span></td></tr>
		</tr>
		
	</tbody>
</table>

<br>

		@if(!empty(@$master->MarksheetStudent->challan_tid) && !empty (@$master->MarksheetStudent->submitted ))
		<div><span class="fonthead"><span class='customStrong'><a style="font-size:15px;">ट्रांजैक्शन विवरण</a>(Transaction Details)</span></span></div>

		<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">&nbsp;Payment Date</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp;Challan Number</span></span></td>
		</tr>
		<tr>
			
			<td>{{@date("d-m-Y h:i:sa",@strtotime(@$master->MarksheetStudent->submitted))}}</td>
			<td>{{@$master->MarksheetStudent->challan_tid}}</td>
		</tr>
	</tbody>
</table>
 
@endif 

<div><span class="fonthead"><span class='customStrong'><a style="font-size:15px;">लॉक एवं सब्मिट विवरण</a>(Lock and Submit Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span class="font">Date</span></span></td>
			<td><span class='customStrong'><span class="font">&nbsp; Is Lock and submit</span></span></td>
		</tr>
		<tr>
			<td>&nbsp;{{@date("d-m-Y h:i:sa",@strtotime(@$master->MarksheetStudent->locksubmitted_date))}}</td>
			@if(@$master->MarksheetStudent->locksumbitted == 1)
			<td>&nbsp; Yes</td>
		    @else
			<td>&nbsp;No</td>
			@endif
			
		</tr>
	</tbody>
</table>
<br>
<div><span class="fonthead"><span class='customStrong' style="font-size:15px;"><a >घोषणा </a>(Decleration)</span></span> &nbsp;
</div>
				<table style="width:100%;">
					<tr>
						<td>
							<ul>
								<li>
									
									<span style="font-size:15px;font-family: Hind;">
										प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्ठियाँ सही भरी गई है। मेरे द्वारा दी गई सभी प्रविष्टियों में कोई त्रुटि पाऐ जाने पर आवेदन रद्द होने का उत्तरदायित्व मेरा रहेगा।
									</span>
								</li>
								<li>
									
									<span style="font-size:15px;font-family: Hind;">
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