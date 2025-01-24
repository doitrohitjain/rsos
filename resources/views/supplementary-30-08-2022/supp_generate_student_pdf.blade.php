<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
 <link href="https://fonts.googleapis.com/css?family=Noto+Sans&subset=devanagari" rel="stylesheet">
<style type="text/css">
	@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
table{
  border: 1px solid black;
  border-collapse: collapse;
  font-size:13px;
  font-family: 'Noto Sans', sans-serif;
 }
 .cc51 {
  font-size: 15px;
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}
</style>
</head>
<body style="margin-top:-5%,margin-bottom:0%">
<br>
<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 40px; height: 40px; border-radius: 10px" alt="image upload button"/>
  </div>                 
 RSOS Supplementary Form For March-May {{ now()->year }} Exam
 <button class="noprint" onClick="window.print()"><span > Print this page</span></button>&nbsp;&nbsp;
 <a class="btn btn-primary noprint" href="{{ URL::previous() }}">Go Back</a>
</div>
<br>
<div><span style="font-size:12px;font-family: Noto Sans,sans-serif;"><span class='customStrong'>व्यक्तिगत विवरण(Personal Details)</span></span></div>

<table border="1" style="width:100%;">
			<tbody>
			<tr>
			<td colspan="4"><span style="font-size:14px;"><span class='customStrong'>Enrollment Number:</span> {{@$master->supplementary->enrollment}}</span></td>
			</tr>

			</tbody>
		<tr>
			<td><span style="font-size:10px;"><span class='customStrong'>आवेदक का नाम (Applicant&#39;s Name):</span></span></td>
			<td><span style="font-size:10px;">{{@$master->name}}</span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>पिता का नाम (Father&#39;s Name):</span></span></td>
			<td><span style="font-size:10px;">{{@$master->father_name}}</span></td>
		</tr>
		<tr>
			<td><span style="font-size:10px;"><span class='customStrong'>माँ का नाम (Mother&#39;s Name): </span></span></td>
			<td><span style="font-size:10px;">{{@$master->mother_name}}</span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>लिंग( Gender):</span> </span></td>
			<td><span style="font-size:10px;">{{@$gender_id[@$master->gender_id]}}</span></td>
		</tr>
		<tr>
			<td><span style="font-size:10px;"><span class='customStrong'>जन्म तिथि ( Date of Birth):</span></span></td>
			<td><span style="font-size:10px;">{{@$master->dob}}</span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>मोबाइल नंबर (Mobile Number):</span> </span></td>
			<td><span style="font-size:10px;">{{@$master->mobile}}</span></td>
		</tr>
	</tbody>
</table><br>
<div><span style="font-size:12px;font-family: Hind;"><span class='customStrong'>&nbsp; विषय विवरण(Subject Details)</span></span></div>
















<div><span style="font-size:12px;font-family: Hind;"><span class='customStrong'>&nbsp;अनिवार्य विषय(Compulsory Subject)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span style="font-size:10px;"><span class='customStrong'>Compulsory Subject</span></span></td>
		</tr>-->
		<tr>
			@if(!empty($result))
			@foreach($result as $key => $values) 
			<td><span class='customStrong'><span style="font-size:10px;">Subject{{ @$key }}</span></span></td>
			@endforeach
			@endif

		</tr>
		
		<tr>
			@if(!empty($result))
			@foreach($result as $key => $values) 
			<td><span style="font-size:10px;">{{ @$subject_list[$values['subject_id']] }}&nbsp;&nbsp;<span style="font-weight: bold;">{{@$values['final_result'] === "p" ? "(Pass)" : "" }}</span></span></td>
			@endforeach
			@endif

		</tr>
			
	</tbody>
</table><br>
 
 @if($mastersuppcount > 0)
	<div><span style="font-size:12px;font-family: Hind;"><span class='customStrong'>&nbsp;अतिरिक्त विषय(Additional Subject)</span></span></div> 
	<table border="2" style="width:100%;">
		<tbody>  
			<tr>
				@for($i=1;$i <= $mastersuppcount;$i++) 
				<td><span class='customStrong'><span style="font-size:10px;">Subject {{ $i }}</span></span></td>
				@endfor
			</tr> 
			<tr>
				@foreach(@$mastersupp as $key => $values) 
					<td>
						<span style="font-size:10px;">
							{{ $subject_list[$values->subject_id] }}
						</span>
					</td>
			 	@endforeach
			</tr>
		</tbody>
	</table> 
@endif
<br>

<div><span style="font-size:12px;font-family: Hind;"><span class='customStrong'>परीक्षा शुल्क विवरण(Exam Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span style="font-size:10px;"><span class='customStrong'>Subject Change Fees</span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Exam Fees &nbsp; </span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Practical Fees</span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Late Fees</span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Forwarding Fees </span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Online Services Fees</span></span></td>
			<td><span style="font-size:10px;"><span class='customStrong'>Tota</span></span></td>
		</tr>
		<tr>
			<td style="font-size:10px;">{{@$master->supplementary->subject_change_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->exam_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->practical_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->late_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->forward_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->online_fees}}</td>
			<td style="font-size:10px;">{{@$master->supplementary->total_fees}}</td>
		</tr>
	</tbody>
</table><br>
<div><span style="font-size:12px;"><span class='customStrong'>Lock & Submtted Details</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span style="font-size:10px;">Lock & Submtted </span></span></td>
			<td><span class='customStrong'><span style="font-size:10px;">Lock & Submtted Date</span></span></td>
		</tr>
		<tr>
			@if(!empty(@$master->supplementary->locksumbitted == 1))
			<td>Yes</td>
			@else
			<td>-</td>
			@endif
			@if(!empty(@$master->supplementary->locksubmitted_date))
			<td>{{@$master->supplementary->locksubmitted_date}}</td>
			@else
			<td>-</td>
			@endif
		</tr>
	</tbody>
</table>

<div><span style="font-size:12px;"><span class='customStrong'>Transaction Details</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 50%;"><span class='customStrong'><span style="font-size:10px;">Transaction Id </span></span></td>
			<td><span class='customStrong'><span style="font-size:10px;">Transaction Date</span></span></td>
		</tr>
		<tr>
			@if(!empty(@$master->supplementary->challan_tid))
			<td>{{@$master->supplementary->challan_tid}}</td>
			@else
			<td>-</td>
			@endif
			@if(!empty(@$master->supplementary->submitted))
			<td>{{@$master->supplementary->submitted}}</td>
			@else
			<td>-</td>
			@endif
		</tr>
	</tbody>
</table><br><br><br><br>
<table style="width:100%; border: none;">
	<tbody>
		<tr>
			<td><span class='customStrong'><span style="font-size:10px;"><span class="nowrap">--------------------------------------------------------------------<br>दिनांक</span></span></span></td>
			<td><span class='customStrong'><span style="font-size:10px;"><span class="nowrap">--------------------------------------------------------------------<br>हस्ताक्षर सदभ कभार मय सील</span></span></span></td>
			<td><span class='customStrong'><span style="font-size:10px;"><span class="nowrap">--------------------------------------------------------------------<br>हस्ताक्षर
			(Signature of Candidate)
			    </br>{{@$master->name}}</span></span></span></td>
			
		</tr>
	</tbody>
</table>

</body>
</html>


 



