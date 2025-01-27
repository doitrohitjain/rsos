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
			<tr>
			<tr style="width:20%;font-size:12px">
				<td style="width: 15%;"><?= $counter; ?> </td>
				<td style="width: 20%;"><?php
					if(!empty(@$student->name) ){
					echo @$student->name;
					} else {
					echo "";
					} 
					?>
				</td>
				<td style="width: 15%;">

					{{ date('d/m/Y', strtotime(@$student->dob)) }}
					
					</td>
				<td style="width: 20%;"><?php
				foreach (@$student->admission_subject as $k => $subject_id){
					echo @$subjectCodes[@$subject_id->subject_id]." "; 
				}
				?></td>
				@if(@$student->document->signature)
				<td rowspan="6" style="width: 10%; text-align: center;"><img alt="100%x200" src="<?php echo url('public/documents/'. $student_id .'/' . @$student->document->signature ) ;?>" class="img-rounded" width="70" style="max-height:70px;"/></td>
				@endif
				@if(@$student->document->photograph)
				<td rowspan="6" style="width: 10%; text-align: center;"><img alt="100%x200" src="<?php echo url('public/'.$studentDocumentPath .'/' . $student_id .'/' . @$student->document->photograph ) ;?>" class="img-rounded" width="70" style="max-height:70px;"/></td>
				@endif
			</tr>
			<tr style="width:20%;font-size:12px">
				<td><?php
					if(!empty(@$student->enrollment) ){
					echo @$student->enrollment;
					} else {
					echo "";
					} 
					?>
				</td>
				<td>
					<?php
					if(!empty(@$student->father_name) ){
					echo @$student->father_name;
					} else {
					echo "";
					} 
					?></td>
				<td>
					@if(@$student->gender_id && @$student->application->category_a)
					{{ @$genders[$student->gender_id] }}
					/
				{{ @$categorya[$student->application->category_a] }}
				@else
				
				@endif
			</td>
				<td><?php 
						if(!empty(@$student->exam_subject)){ ?>
							<?php
				foreach (@$student->exam_subject as $k => $subject_id){
					echo @$subjectCodes[@$subject_id->subject_id]." "; 
				}
				?> 
					<?php 	} else {
							echo "";
						} 
					?></td>
			</tr>
			<tr style="width:20%;font-size:12px">
				<td>
					<?php
					if(!empty(@$student->adm_type) ){
					echo @$adm_types[$student->adm_type];
					} else {
					echo "";
					} 
					?></td>
				<td><?php
					if(!empty(@$student->mother_name) ){
					echo @$student->mother_name;
					} else {
					echo "";
					} 
					?></td>
				<td>
					<?php 
						if(@$midium[@$student->application->medium]){
							echo @$midium[@$student->application->medium];
						}else{
							echo "";
						}
					?> 
					/ 
					<?php 
						if(@$student->course){
							echo @$student->course . "<sup>th</sup>"; 
						}else{
							echo "";
						}
					?> 
					</td>
					<td>
						<?php
							if(@$course == 10){
								
							}elseif(@$course == 12){
								if(@$student->application->year_pass){ 
									echo @$boards[$student->application->board] ."/".@$rsos_yearsstudent[$student->application->year_pass];
								} else {
									echo "";
								} 
							}
							/* if(@$student->application->pre_qualification){
								echo "(".@$student->application->pre_qualification."th)";
							} else {
								echo "-";
							} */
						?>
                   </br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php 
					if(!empty(@$student->studentfees->total) ){
						echo @$student->studentfees->total;
					} else {
						echo "";
					} 
					?>

					</td>

			</tr>

			<tr style="width:20%;font-size:12px">
				<td rowspan="1"><?php 
						if(@$student->application->toc && @$student->application->toc == 1){
							echo @$boards[@$student->toc->board] ."/". @$tocpassfail[@$student->toc->year_fail] .
							@$tocpassyear[@$student->toc->year_pass];
						} else {
							echo "";
						} 
					?></td>
					<td  rowspan="1">
						
						<table>
							<?php 
						 if(!empty(@$student->toc_subject)){ ?>
							<?php
			          foreach (@$student->toc_subject as $k ){ ?>
								<tr>
									<td>
					     <?php echo @$subjectCodes[@$k->subject_id]  . " / ".  @$k->theory  ." / ".  @$k->practical  ." / ".  @$k->total_marks; ?>
										</td>
								</tr>
								   <?php  }
				    }
				     ?> 
						</table>

					</td>
			</tr>
			<tr style="width:20%;font-size:12px">
				<td colspan="3" rowspan="1" >
					<?php 
					if(!empty(@$student->toc->roll_no)){
					echo @$student->toc->roll_no ;
					} else {
					echo "";
					} 
					?>
					</td>
				<td></td>
			</tr>
			<tr style="font-size:12px">
				<td colspan="6" rowspan="1"><strong>Address</strong>:
				<?php 
					if(!empty($student->Address->address1)){
						if(isset($student['Address']['address1']) && !empty($student['Address']['address1'])){
							echo $student['Address']['address1'] . ',';
						}
						if(isset($student['Address']['address2']) && !empty($student['Address']['address2'])){
							echo $student['Address']['address2'] . ',';
						}
						if(isset($student['Address']['address3']) && !empty($student['Address']['address3'])){
							echo $student['Address']['address3'] . ',';
						}
						if(isset($student['Address']['tehsil_name']) && !empty($student['Address']['tehsil_name'])){
							echo $student['Address']['tehsil_name'] . ',';
						}
						if(isset($student['Address']['city_name']) && !empty($student['Address']['city_name'])){
							echo $student['Address']['city_name'] . ',';
						}
						if(isset($student['Address']['district_name']) && !empty($student['Address']['district_name'])){
							echo $student['Address']['district_name'] . ',';
						}
						if(isset($student['Address']['state_name']) && !empty($student['Address']['state_name'])){
							echo $student['Address']['state_name'] . '-';
						}
						if(isset($student['Address']['pincode']) && !empty($student['Address']['pincode'])){
							echo $student['Address']['pincode'] . '';
						}
						}else{
						echo "-";	
						}								
					?> </td>
			</tr>
		</tbody>
	</table> 
 <?php } } ?>
    </body>
<html>


                       
