

<html>
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<style>
	td{
		vertical-align:text-top !important;
		width:20% !important;
		font-size:12px!important;
	}
	tr{
		width:20% !important;
		font-size:12px!important;
	}
	table.bottomBorder { 
    border-collapse: collapse; 
  }
  table.bottomBorder td, 
  table.bottomBorder th { 
    border-bottom: 1px solid grey; 
    padding: 5px; 
    text-align: left;
  }
  .font{
  	font-family: Arial, sans-serif;
  }
</style>
</head>
<body>
	@php 
		header( 'Content-Type: text/html; charset=utf-8' ); 
	@endphp
<table border="0" style="width:100%;">
<tbody>

<tr>
	<td style="width: 50%;font-size:12px" class="font"><strong>RAJASTHAN STATE OPEN SCHOOL,JAIPUR</strong></td>
	<td style="width: 40%;font-size:12px" class="font">
	<strong>TOC Marks Checklist Stream-1 [<?php if(isset($stream) && $stream == 1){
		echo "March-May"; } else { echo "Oct-Nov";  } ?> <?php echo @$rsos_years[$current_admission_session_id]; ?> ]</strong>
	</td>
	<td style="width: 10%;font-size:12px;text-align:right;" class="font">
	<strong>&nbsp;&nbsp;&nbsp;<?php echo $aicode; ?>/<?php echo $course; ?>th</strong>
	</td>
</tr>

	</tbody>
</table>

<hr />
<table border="0"  style="width:100%;" class="font">
	<tbody>
		<tr>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								S.No.
							</td>
						</tr>
						<tr>
							<td>
								ENR. No	
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								Name
							</td>
						</tr>
						<tr>
							<td>
								Father's Name	
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								TOC Board/Year	
							</td>
						</tr>
						<tr>
							<td>
								Roll No.	
							</td>
						</tr> 
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								SUB1/TH/PR/TOTAL	
							</td>
						</tr>
						<tr>
							<td>
								SUB2/TH/PR/TOTAL	
							</td>
						</tr>
						<tr>
							<td>
								SUB3/TH/PR/TOTAL	
							</td>
						</tr>
						
						<tr>
							<td>
								SUB4/TH/PR/TOTAL	
							</td>
						</tr>
						
						<tr>
							<td>
								SUB5/TH/PR/TOTAL	
							</td>
						</tr>
						
						<tr>
							<td>
								SUB6/TH/PR/TOTAL	
							</td>
						</tr>
						
						<tr>
							<td>
								SUB7/TH/PR/TOTAL	
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								CONV_SUB1/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB2/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB3/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB4/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB5/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB6/TH/PR/TOTAL
							</td>
						</tr>
						<tr>
							<td>
								CONV_SUB7/TH/PR/TOTAL
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr> 
	</tbody>
</table>

<?php if(@$master){ @$counter = 0; 
foreach($master as $key => $student){  
	$student_id =$student->id; 
	$counter++;
?> 
<hr/>

<table border="0"  style="width:100%;" class="font">
	<tbody>
		<tr>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								{{ @$counter }} <br>{{ @$student->enrollment }}
							</td>
						</tr>
						
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								{{ @$student->name }} <br> {{ @$student->father_name  }}	
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td>
				<table>
					<tbody>
						<tr>
							<td>
								<?php 
								if(!empty(@$student->toc->year_pass)){
								if(@$student->application->toc && @$student->application->toc == 1){
								echo @$boards[$student->toc->board]."/".@$tocpassyear[$student->toc->year_pass];
								} else {
									echo "No";
								}
							}elseif(!empty(@$student->toc->year_fail)){
								if(@$student->application->toc && @$student->application->toc == 1){
								echo @$boards[$student->toc->board]."/".@$tocpassfail[$student->toc->year_fail];
								} else {
									echo "No";
								}
							}

								?>	
								<br>{{ @$student->toc->roll_no }}
							</td>
						</tr> 
					</tbody>
				</table>
			</td>
			<td>
				<table class="font">
					<tbody>
						<?php foreach (@$student->toc_subject as $k => $subject_id){  ?>
						<tr>
							<td>
								<?php echo @$subjectCodes[@$subject_id->subject_id];
								echo "/".@$subject_id->theory;
								echo "/".@$subject_id->practical;
								echo "/".@$subject_id->total_marks;
								?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</td>
			<td>
				<table class="font">
					<tbody>
						<?php foreach (@$student->toc_subject as $k => $subject_id){  ?>
						<tr>
							<td>
								<?php echo @$subjectCodes[@$subject_id->subject_id];
								echo "/".@$subject_id->conv_theory;
								echo "/".@$subject_id->conv_practical;
								echo "/".@$subject_id->conv_total_marks;
								?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</td>
		</tr> 
	</tbody>
</table>
<?php } } ?>
		
</body>
<html>