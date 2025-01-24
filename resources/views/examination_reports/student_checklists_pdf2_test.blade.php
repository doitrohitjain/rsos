<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<style type="text/css">
	table.bottomBorder { 
		border-collapse: collapse; 
	}
	thead td {
		word-break:break-all;
	}
	table.bottomBorder td, 
	table.bottomBorder th { 
		border-bottom: 1px solid grey; 
		padding: 5px; 
		text-align: left;
	}
	.page-break {
		page-break-after: always;
		page-break-inside:avoid;
	}
	body {
 		font-family: Arial, sans-serif;
}
</style>
</head>
<body>
<table style="width:100%;">
	<thead>
		<tr>
			<td style="width: 50%;font-size:14px"><strong> 
				RAJASTHAN STATE OPEN SCHOOL,JAIPUR
				</strong></td>
			<td style="width: 40%;font-size:14px"><strong>Registration Checklist
			@if(@$stream == 1)
		    (Stream {{@$stream}})[Total : {{ count(@$master) }}]
		    @elseif(@$stream == 2)
		    (Stream {{@$stream}})[Total : {{ count(@$master) }}]
		    @endif
		   	</strong></td>
			<td style="width: 10%;font-size:15px;text-align:right;"><strong>&nbsp;&nbsp;&nbsp;<?php echo @$reportname;?>/<?php echo @$course;?><sup>th</sup></strong></td>
		</tr>
	</thead>
</table>
<hr/>
<table border="0" style="width:100%; height:95px">
	<thead>
		<tr>
		<tr style="width:20%;font-size:12px">
			<td style="width: 15%;">S.No.</td>
			<td style="width: 20%;">Name</td>
			<td style="width: 15%;">DOB/Age</td>
			<td style="width: 20%;">Subject / Add. Subjects</td>
			<td rowspan="6" style="width: 10%; text-align: center;">Signature</td>
			<td rowspan="6" style="width: 10%; text-align: center;">Photograph</td>
		</tr>
		<tr style="width:20%;font-size:12px">
			<td>ENR. No</td>
			<td>Father&#39;s Name</td>
			<td>Gender/Category</td>
			<td>Exam Subjects</td>
		</tr>
		<tr style="width:20%;font-size:12px">
			<td>App. Type</td>
			<td>Mother&#39;s Name</td>
			<td>Medium/Course</td>
			<td>
				@php 
				if(@$course == 10){
				
				}
				if(@$course == 12){
					echo "Board". "/" . "Year Pass X";
				}
				@endphp
			</td> 
		</tr>
		<tr style="width:20%;font-size:12px">
			<td style="width:20%;">TOC Board/Year</td>
			<td>
				<table>
					<tr>SUB / TH / PR / TOT</tr>
				</table></td>
			<td>&nbsp;</td>
			<td>Fee [AI/RSOS/Difference]</td>
		</tr>
		<tr style="width:20%;font-size:12px">
			<td>Roll No.</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Error Parameters</td>
		</tr>
		<tr style="font-size:12px">
			<td colspan="4" rowspan="1"><strong>Address</strong>: </td>
		</tr>
	</thead>
</table>
<?php if(@$master){ 
	@$counter = 0;
foreach(@$master as $key => $student){  
	@$student_id =$student->id; 
	@$counter++; 
?> 
	<hr/>
	<table border="0" style="width:100%; height:95px;page-break-inside: avoid">
		<tbody> 
			<tr style="width:20%;font-size:12px"> 
				@if(@$student->document->signature)
				<td rowspan="6" style="width: 10%; text-align: center;"><img alt="100%x200" src="<?php echo url('public/documents/'. $student_id .'/' . @$student->document->signature ) ;?>" class="img-rounded" width="70" style="max-height:70px;"/></td>
				@endif
				@if(@$student->document->photograph)
				<td rowspan="6" style="width: 10%; text-align: center;"><img alt="100%x200" src="<?php echo url('public/'.$studentDocumentPath .'/' . $student_id .'/' . @$student->document->photograph ) ;?>" class="img-rounded" width="70" style="max-height:70px;"/></td>
				@endif
			</tr>  
		</tbody>
	</table> 
 <?php } } ?>
    </body>
<html>


                       
