<html>
<head>
<style>
.dottedborderstyle{
	border-top: 4px solid black !important;
	margin-top:5px !important;
	margin-bottom:5px !important;
	margin-left:2px !important;
	margin-right:2px !important;
}
#instructions ol li{margin-top:4px;font-size:15px;}

.hallticketbackcss{
	text-align:center;
}
.hallticketbackcss div{
	font-weight:bold;
}
.leftTd{
	text-align:left;
	padding-left:10px;
}
.rightTd{
	text-align:right;
	padding-right:10px;
}
.page-header{
	margin:0px 0px 0px 0px;
	padding:0px 0px 0px 0px;		
	
}
.page-headerh5{
	margin:0px 0px 0px 0px;
	padding:0px 0px 0px 0px;
	font-size:15px;
	font-weight:normal;
}
.page-headerh4{
	margin:20px 0px 0px 0px;
	padding:0px 0px 0px 0px;
	font-size:16px;
	font-weight:normal;
	
}
h2{font-size:17px;margin:10px 0px 5px 0px;}
td h4{font-size:13px;margin:10px 0px 5px 0px;}

.page-break {
	page-break-after: always;
	page-break-inside:avoid;
}

.font{
 font-family: Arial, sans-serif;
 }
 .fontsize{
 font-size: 14px;	
 }
 .fontcapitilize{
 	text-transform: uppercase;
 }
	
</style>
</head>

<body class="font">
	@php 
		use App\Helper\CustomHelper;
		if(isset($students) && !empty($students)){
			foreach($students as $courseval => $studentCourses){
				$counter = 1; $hallticketcounter = 1;
				foreach($studentCourses as $skey => $student){ 
				
				
				
				
						
	@endphp


	<div class="admitCard">
		<div class="row" style="border-style: groove;margin-top:5px;margin-left:5px;margin-right:5px;height:50%px;page-break-inside: avoid" "="">

			<table cellspacing="0" style="height:25px;">
				<tbody>
					<tr>
						<td style="width:10%">
							<p><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px; height:60px;  border-radius: 10px ;padding:15px;" alt="image upload button"></p>
						</td>
						<td style="text-align:center; vertical-align:middle">
							<span style="font-size:22px;font-weight:bold;"><b>RAJASTHAN STATE OPEN SCHOOL,JAIPUR <b><span><br>
							<h4 style="font-size:14px;color:#665c5c;margin-top:0px !important;">
							<?php 
								if($stream==1){
								 echo $exam_session[$stream];
								  }
								if($stream==2){
								 echo $exam_session[$stream]; 
								}
									
								if ($student['course'] == 10){ ?> 
									Secondary	
								<?php } else if ($student['course'] == 12) { ?>
									Senior Secondary
								<?php } ?> Examination, (Hall Ticket)
							</h4>
						</span></b></b></span></td>
					</tr>
				</tbody>
			</table>
			
			<div class="col-md-12" > 
				<div class="col-xs-12" style="height:140px">
					<div class="row">
						<table style="width:100%;">
							<tbody><tr>
								<td style="width:60%;">								
									<table class="table2" role="grid" aria-describedby="example2_info" style="width:100%;font-size:10px;">
										<tbody>
											<tr style="margin-top:20px;">
												<td colspan="4">
												<h4 style="font-size:14px;font-weight:bold;text-transform: uppercase;"> CENTRE:
													<?php echo ($student['course'] == 10) ? $student['ecenter10'] : $student['ecenter12']; ?>-<?php echo ucwords($student['cent_name']); ?>
													<?php 
														//echo ucwords($student['cent_add1']);
														//echo ucwords($student['cent_add2']);
													?>
												</h4>
												</td>
											</tr>
											<tr>
												<th style="text-align:left;font-size:12px;">Code</th>
												<th style="text-align:left;font-size:12px;">Subject [ T : Theory, P : Practical ]</th>
												<th style="text-align:left;font-size:12px;">Date</th>
												<th style="text-align:left;font-size:12px;">Exam.Time</th>
											</tr>								
											<?php
											if(isset($student['exam_subjects']['T']) && !empty($student['exam_subjects']['T'])){
											foreach (@$student['exam_subjects']['T'] as $key => $subject){ 
											
										
											   
												$suppFlag = false;
												$result = CustomHelper::getStudentResult($student['student_id'], $subject['id']);


											 
												if (in_array($subject['id'], $practicalsubjects12) && $student->type == 'Supplementary' && $result == 777){
													$suppFlag = true;
												}
											
												$paperCodes = array();
												if ((@$student['exam_subjects']['T'][$key]) && $result != 666){
													$paperCodes[] = 'T';
												}
												if ((@$student['exam_subjects']['P'][$key]) && $result != 777 ){
													$paperCodes[] = 'P';
												}

											
												$strpapercode = '[' . join(',', array_values($paperCodes)) . ']';
											?>
											<tr>
												<td class='fontsize'><?php echo $key; ?></td>
												<td class='fontsize'><?php echo $subject['name'] . ' ' . $strpapercode; ?></td>
												<td class='fontsize'>
												<?php
												if ($result != 666){
													if(isset($subject['exam_date']) && !empty($subject['exam_date'])){
														echo date('d-m-Y', strtotime($subject['exam_date']));
													}
													
												}
												?>
												</td>
												<td class='fontsize'>														
												<?php
												if ($result != 666){
													if(isset($subject['exam_time_start']) && !empty($subject['exam_time_start']) && isset($subject['exam_time_end']) && !empty($subject['exam_time_end']) ){
														echo @$exam_time_table_start_end_time[$subject['exam_time_start']]; ?> TO <?php echo @$exam_time_table_start_end_time[$subject['exam_time_end']];	
													}
													
												}
												?>
												</td>
											</tr>
											<?php } }?>

										</tbody>
									</table>
								</td>
							
								<td style="width:20%;">
									<table id="example2" class="table2" role="grid" aria-describedby="example2_info" style="width:100%;padding-right:15px;">
										<tbody>
											<tr>
												<td>
													<div>
														<?php

															$studentDocumentPath = "documents/". $student['student_id'];
															$fld = 'photograph';$lbl = __('Photograph'); $path = $student[$fld];
															?>
														<span>

														@if(!empty($student['photograph']))
															<img alt="image" src="{{asset('public/documents/'.$student['student_id'].'/'.$student['photograph'])}}" width="100px"  height="100px">
														@else
															 <img alt="image" height="100px" src="{{asset('public/app-assets/images/studentuser.png')}}" width="100px">  
														@endif
														</span> 
													</div>
											
													<div>
														<?php  
															$fld = 'signature';$lbl = __('Signature'); $path = $student[$fld]; 
														?>
														<span>
														@if(!empty($student['signature']))
														<img alt="image" src="{{asset('public/documents/'.$student['student_id'].'/'.$student['signature'])}}" width="100px" style="max-height:50px;">
														@else
															 <img alt="image" height="20px" src="{{asset('public/app-assets/images/studentsignature.png')}}" width="100px">
														@endif
														</span>  
													</div>
												</td> 
												<td style="margin-right:25px;">
													<table>
														<tbody>
															<tr>
																<th style="font-size:22px;text-align:left"><b>Roll No.: {{ @$student['enrollment'] }}</b></th>
															</tr>
															<tr>	
																<td class='fontcapitilize' style="font-size:14px;">
																	 {{ @$student['name'] }}  							
																</td>
															</tr>
															<tr>	
																<td class='fontcapitilize' style="font-size:14px;">
																	 {{ @$student['father_name'] }}	
																								
																</td>
															</tr>
															<?php if (isset($student['tehsil']) && $student['tehsil'] != ''){ ?>
															<tr>	
																<td style="font-size:14px;">
																	Block/Teh. : {{ @$student['tehsil'] }}		
																</td>
															</tr>
															<?php } ?>
															<?php if (isset($student['district']) && $student['district'] != ''){ ?>
															<tr>	
																<td style="font-size:14px;">
																	Distt. : {{ @$student['district'] }}								
																</td>
															</tr>
															<?php } ?>
															<?php if (isset($student['dob']) && $student['dob'] != "") { ?>
															<tr>	
																<td style="width:100%;font-size:14px;">
																	<?php
																	echo  'DOB.:'.@$student['dob'];
																	/*$dd = explode("/", $student['dob']);
																	if(isset($dd[2])){
																	echo $date = $dd[0] . "-"  . $dd[1] . "-" . $dd[2];
																	}*/
																	?>
									
																</td>
															</tr>
															<?php } ?>
														</tbody>
													</table>
												</td>
											</tr>
											<!--<tr>
												<td class="leftTd" colspan=2>
																				
												</td>
											</tr>-->
										</tbody>
									</table>
								</td>
							</tr>
						</tbody></table>
					</div>
				</div>
				<div class="row" style="margin-top:55px;">
					<div class="col-sm-12">
						<table class="col-sm-12" >
							<tbody>
								<tr><td style="font-size:12px; margin-top:2px;"> <b>Schedule for Practical Examination</b> :</td></tr>
								<?php
								if ((@$student['exam_subjects']['P'])){
									echo "<table class='col-sm-12;' style='font-size:12px;'><tr>";
									$n = 1;
								
									foreach ($student['exam_subjects']['P'] as $key => $subject){
										$suppFlag = false;
										$result = CustomHelper::getStudentResult($student['student_id'], $subject['id']);
									
										if (array_key_exists($subject['id'], $practicalsubjects12) && $student['type'] == 'Supplementary' && $result == 777){

											$suppFlag = true;
										} 
									
										if ($n % 2 > 0){
											echo "</tr><tr>";
										}
										?>
										@if($suppFlag === false)
											<td class="col-sm-6" style='font-size:12px; padding-left: 10px;'>
												
												@if(isset($subject['exam_date_start']) && !empty($subject['exam_date_start']) && isset($subject['exam_date_end']) && !empty($subject['exam_date_end']))
													@php echo date('d-m-Y', strtotime($subject['exam_date_start'])); ?>&nbsp; TO &nbsp;<?php echo date('d-m-Y', strtotime($subject['exam_date_end'])); 
													@endphp : 
												@endif 
												@if(isset($subject['name']) && !empty($subject['name']))
													<?php echo ucwords($subject['name']); ?> 
												@endif 
											</td> 					
										@endif
										<?php   $n++; 
									}
									echo "</tr></table>";
								} 
								?>
						</table>								
					</div>							
				</div>
				<div class="fontsize" style="border-top:1px solid lightgrey;">
					<span style="margin-left:10px;"><b>परीक्षार्थियों के लिए निर्देशः</b></span>
				</div>
				<div class="row" style="height:180px;">
					<!--<table class="table2 fontsize" id="instructions" role="grid" aria-describedby="example2_info" style="width:100%;padding:0px;">
						<tbody>
							<!--<tr>
								<td class="hallticketbackcss">
								<div>RAJASTHAN STATE OPEN SCHOOL</div>
								<div>Dr. R.K. Shiksha Sankul</div>
								<div>J.L.N. Marg, Jaipur-302017</div>
								<div>MARCH-MAY 2019 Examination (Hall Ticket)</div>
								</td>													
							</tr>-->
							<!-- <tr>
								<td colspan="4" style="text-align:left;">
									
								</td>
							</tr>  
							<tr>
								<td colspan="4"-->
									
									<img alt="materialize logo" height="180" width="100%" src="{{asset('public/app-assets/images/hallticketinstructions_new.png')}}" />					
								<!--</td>  
							</tr>
						</tbody><tbody>
					</tbody></table>-->
				</div>
			</div>
			
			<table style="margin-top:30px">
				<tbody>
					<tr>
						<th style="padding-left:100px;"><img src="{{asset('public/app-assets/images/favicon/old-DirectorSign.png')}}" alt="Logo" style="width: 75px; height: 36px; "></th>
						<th style="padding-left:450px;margin-top:5px;"><img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" alt="Logo" style="width: 75px; height: 36px;"></th>
					</tr>
					<tr>
						<td style="padding-left:100px;">Asstt.Director Exam.</td>
						<td style="padding-left:450px;">Secretary</td>
					</tr>
					<div class="col-sm-12" style="float:right;margin-right: 10px;"><?php echo $hallticketcounter ;?> </div>
				</tbody>
			</table>

		</div>
	</div>
	
	<?php if($counter %3!=0){ ?>
		<!--<div class="dottedborderstyle"></div>-->
	<?php } ?>
	</div>
			
	<?php
	$hallticketcounter++;
	$counter++;

	if($counter %2!=0){ $counter = 1; ?>
	<!--<div style = "clear:both; page-break-after:always;"></div>-->
	<?php   }  } ?>		
	<!--<div style = "clear:both; page-break-after:always;"></div>-->
	<?php } }  else {
		echo 'No Record Found!';
	} ?>

