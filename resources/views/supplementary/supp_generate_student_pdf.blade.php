@extends('layouts.pdf')
@section('content')

<style type="text/css">
	table{
		border-collapse: collapse;
		border-spacing: 0;
		font-family: arial, sans-serif;
	}
 .cc51 {
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}
.font{
 font-family: Arial, sans-serif;
 }

</style>

<body style="margin-top:-5%,margin-bottom:0%">
<br>
<div class="cc51"> 
 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px; height: 60px; border-radius: 10px" alt="image upload button"/>
  </div>                 
 <span style=" font-size:28px" class="font">

 Supplementary Form For {{ @$displayExamMonthYear }} Exam</span>
 <!--<button class="noprint" onClick="window.print()"><span > Print this page</span></button>&nbsp;&nbsp;
 <a class="btn btn-primary noprint" href="{{ URL::previous() }}">Go Back</a>-->
</div>
<hr>
<!--
<table border="0" style="width:100%">
	<tr>
		<td>
			<span style="color: red; font-size:12px;">Notes:- अग्रेषण शुल्क रु. 5/- और ऑनलाइन सेवा शुल्क रु. 25/- एआई सेंटर को ऑफलाइन भुगतान करना होगा <span style="color: red; font-size:10px;"> ( Forwarding Fees of Rs. 5/- and Online Service Fees of Rs. 25/- to be paid offline to the AI center).</span></span>
		</td>
	</tr>
</table> -->
<center>
  
 
<table border="0" style="width:97%">
	<tr>
		<td style="font-size:16px;;">नामांकन (Enrollment No) :  </td><td style="font-size:16px;;font-weight:bold;">{{@$master->supplementary->enrollment}} </td>
		<td style="font-size:16px;;">सत्यापन स्थिति(Verification Status) : </td>
			@if($master->supplementary->is_department_verify == 2 && $master->supplementary->is_aicenter_verify == 2)
			<td style="font-size:16px;;font-weight:bold;color:green;"> Approved</td>
		@else
			<td style="font-size:16px;;font-weight:bold;color:red;"> Not Approved</td>
		@endif
	</tr>
	<tr>
	  <td style="font-size:16px;;">नाम (Name) :  </td><td style="font-size:16px;;font-weight:bold;">{{@$master->name}} </td>
	  
	  <td style="font-size:16px;;">पिता का नाम (Father&#39;s Name) :  </td><td style="font-size:16px;;font-weight:bold;">{{@$master->father_name}} </td>
	</tr>
	<tr>
	  <td style="font-size:16px;;">माँ का नाम (Mother&#39;s Name) : </td><td style="font-size:16px;;font-weight:bold;"> {{@$master->mother_name}} </td>
	  <td style="font-size:16px;;">लिंग( Gender) : </td><td style="font-size:16px;;font-weight:bold;"> {{@$gender_id[@$master->gender_id]}} </td>
	</tr>
	 <tr>
	  <td style="font-size:16px;;">जन्म तिथि ( Date of Birth) : </td><td style="font-size:16px;;font-weight:bold;">{{ date("d-m-Y",strtotime(@$master->dob)); }} </td>
	  <td style="font-size:16px;;">मोबाइल नंबर (Mobile Number) :</td><td style="font-size:16px;;font-weight:bold;">  {{@$master->mobile}} </td>
	</tr> 
	 <tr>
	  <td style="font-size:16px;;">विद्यार्थी द्वारा भरा गया?(Filled in by the student?) : </td><td style="font-size:16px;;font-weight:bold;">{{ @$yesno[@$master->supplementary->is_self_filled]}} </td>
	  <td style="font-size:16px;;">एसएसओ (ssoid) :</td><td style="font-size:16px;;font-weight:bold;">  {{@$master->ssoid}} </td>
	</tr> 
  
    <tr>
	<td style="font-size:16px;;">विद्यार्थी पात्र है(Is Eligible) : </td><td style="font-size:16px;;font-weight:bold;">{{ @$yesno[@$master->supplementary->is_eligible]}} </td>	
    </tr>
	 @if($master->course == 12)
	<tr>
	  <td style="font-size:16px;;">पूर्व योग्यता (Pre Qualification) : </td><td style="font-size:16px;;font-weight:bold;">{{@$pre_qualifi[$master->application->pre_qualification]}} </td>
	  <td style="font-size:16px;;">वर्ष पास (Year Pass) :</td><td style="font-size:16px;;font-weight:bold;">  {{@$rsos_years[$master->application->year_pass]}} </td>
	</tr>
  
   @endif
  
	
  </table>



</center>

<hr>
<br>


<div><span style="font-size:18px;" class="font">&nbsp; विषय विवरण(Subject Details)</span></div>


<div><span style="font-size:16px;" class="font">&nbsp;अनिवार्य विषय(Compulsory Subject)</span></div>
<table border="1" style="width:100%;">
		<!--<tr>
			<td colspan="5"><span style="font-size:10px;"><span class='customStrong'>Compulsory Subject</span></span></td>
		</tr>-->
		<tr>
			@if(!empty($result))
			@foreach($result as $key => $values) 
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Subject {{ @$key + 1 }}</td>
			@endforeach
			@endif

		</tr>
		
		<tr>
			@if(!empty($result))
			@foreach($result as $key => $values) 
			@if(@$values['final_result'] === "P")
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$subject_code_list[$values['subject_id']] }}&nbsp;&nbsp;<span style="font-weight: bold;">{{@$values['final_result'] === "P" ? "(Pass)" : "" }}</span></td>
			@else
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$subject_list[$values['subject_id']] }}&nbsp;&nbsp;<span style="font-weight: bold;">{{@$values['final_result'] === "P" ? "(Pass)" : "" }}</span></td>
			@endif

			@endforeach
			@endif 
		</tr>
</table><br>
 
 @if($mastersuppcount > 0)
	<div><span style="font-size:16px;" class="font">&nbsp;अतिरिक्त विषय(Additional Subject)</div> 
	<table border="1" style="width:100%;">
		
			<tr>
				@for($i=1;$i <= $mastersuppcount;$i++) 
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">Subject {{ $i }}</td>
				@endfor
			</tr> 
			<tr>
				@foreach(@$mastersupp as $key => $values) 
					<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">
							{{ $subject_list[$values->subject_id] }}
					</td>
			 	@endforeach
			</tr>
		
	</table> 
@endif
<br>

<div><span style="font-size:18px;" class="font">परीक्षा शुल्क विवरण(Exam Fees Details)</span></div>

<table border="1" style="width:100%;">
		<tr>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Subject Change Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Exam Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Practical Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Late Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Forwarding Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Online Services Fees</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Total</td>
		<tr>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->subject_change_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->exam_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->practical_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->late_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->forward_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->online_fees}}</td>
			<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->total_fees}}</td>
		</tr>

</table><br>
<div><span style="font-size:18px;" class="font">Lock & Submitted Details</span></div>

<table border="1" style="width:100%;">
	
		<tr>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Lock & Submitted</td>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Lock & Submitted Date</td>
		</tr>
		<tr style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">
			@if(!empty(@$master->supplementary->locksumbitted == 1))
			<td>&nbsp;Yes</td>
			@else
			<td>-</td>
			@endif
			@if(!empty(@$master->supplementary->locksubmitted_date))
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;
			{{@ date("d-m-Y H:i:s",strtotime($master->supplementary->locksubmitted_date)) }}</td>
			@else
			<td>-</td>
			@endif
		</tr>
	
</table>
 &nbsp;
<div><span style="font-size:18px;" class="font">Transaction Details</span></div>

<table border="1" style="width:100%;">
	     @if(!empty(@$master->supplementary->update_supp_change_requests_challan_tid))
		<tr>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Change Request Transaction Id </td>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp; Change Request Transaction Date</td>
		</tr>
		<tr>
			@if(!empty(@$master->supplementary->update_supp_change_requests_challan_tid))
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->update_supp_change_requests_challan_tid}}</td>
			@else
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">-</td>
			@endif
			@if(!empty(@$master->supplementary->update_supp_change_requests_submitted))
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@ date("d-m-Y H:i:s",strtotime($master->supplementary->update_supp_change_requests_submitted)) }}</td>
			@else
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">-</td>
			@endif
		</tr>
		@endif
		 @if(!empty(@$master->supplementary->challan_tid))
		<tr>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Transaction Id </td>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;Transaction Date</td>
		</tr>
		<tr>
			@if(!empty(@$master->supplementary->challan_tid))
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$master->supplementary->challan_tid}}</td>
			@else
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">-</td>
			@endif
			@if(!empty(@$master->supplementary->submitted))
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@ date("d-m-Y H:i:s",strtotime($master->supplementary->submitted)) }}</td>
			@else
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">-</td>
			@endif
		</tr>
		@endif
	
</table><br>


<!--
<div><span style="font-size:18px;" class="font">Verfication Status : Pending/Approved/Rejected</span></div>
<table border="1" style="width:100%;">
	@if(@$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status == 2 && @$suppVerifcationData->department_status && @$suppVerifcationData->department_status == 2)
		<tr>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">Final Verfication : </td>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> Approved</td>
		</tr>
	@else
		<tr>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">Final Verfication : </td>
			<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> Not Approved</td>
		</tr>
	@endif 
</table>
-->
<!-- 
@if(!empty($role_id) && $role_id != Config::get("global.student"))
	
	@if($role_id == Config::get("global.aicenter_id"))
		<table border="1" style="width:100%;"> 
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;एआई केंद्र द्वारा सत्यापित है ?(Is Verified By Aicenter) </td>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@ @$supp_verfication_status[@$master->supplementary->is_aicenter_verify] }}</td>
			</tr> 
		</table>
		<table border="1" style="width:100%;">
			@if(@$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status == 3 && $master->supplementary->is_aicenter_verify == 3)
				<tr>
					<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;एआई केंद्र द्वारा टिप्पणियाँ( Remarks By AI Center) </td>
				</tr>
				<tr>
					<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;{{ @$suppVerifcationData->aicenter_remark  }}</td>
				</tr>
			@endif 
		</table>
	@else 
		<table border="1" style="width:100%;"> 
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;एआई केंद्र द्वारा सत्यापित है  ?(Is Verified By Aicenter) </td>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@ @$supp_verfication_status[@$master->supplementary->is_aicenter_verify] }}</td>
			</tr>
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;विभाग द्वारा सत्यापित है  ?(Is Verified By Department)</td>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$supp_verfication_status[@$master->supplementary->is_department_verify] }}</td>
			</tr>  
		</table>
		<table border="1" style="width:100%;">
			@if(@$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status && @$suppVerifcationData->aicenter_status == 3 && $master->supplementary->is_aicenter_verify == 3)
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;एआई केंद्र द्वारा टिप्पणियाँ( Remarks By AI Center) </td>
			</tr>
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;{{ @$suppVerifcationData->aicenter_remark  }}</td>
			</tr>
			@elseif(@$suppVerifcationData->department_status && @$suppVerifcationData->department_status && @$suppVerifcationData->department_status == 3 && $master->supplementary->is_department_verify == 3)
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;विभाग द्वारा टिप्पणियाँ( Remarks By Department) </td>
			</tr>
			<tr>
				<td style="font-size:18px; border: 1px solid #dddddd; text-align: left; padding: 8px;">&nbsp;{{ @$suppVerifcationData->department_remark   }}</td>
			</tr>
			@endif 
		</table>
	@endif  
@endif  
-->
<br>

@if(!empty(@$suppVerifcationData))
	
@else
<br><br><br>
@endif
<table style="width:100%; border: none;" class="font">
       <tr>
            <td>--------------------------------------<br>दिनांक</td>
            <td>---------------------------------------------<br>हस्ताक्षर सदभ कभार मय सील</td>
            <td>---------------------------------------------<br>	(Signature of Candidate)</td>
        </tr>
        <tr>
					<td>&nbsp;</td>
        	<td>&nbsp;</td>
           <td>{{$master->name}}</td>
        </tr>
        
    </table>
</body>
@endsection
@section('customjs')

@endsection