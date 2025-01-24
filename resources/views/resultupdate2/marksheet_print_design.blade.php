<?php 
use App\Component\ThoeryCustomComponent;
 $replaceText = "Duplicate";
 //$replaceText = "";
 /*if(isset($this->params['pass'][2]) && $this->params['pass'][2] == 'r'){
	$replaceText = "Revised";
 }*/
 $replaceText = "Revised";

$passfail = null; 

 if(isset($final_result->final_result) && $final_result->final_result != ""){
	$passfail = $final_result->final_result; 	 
 }

if($passfail == 'pass' || $passfail == 'Pass' || $passfail == "PASS"){ ?> 

<table width="100%">
<tr>
<td style="height:90px;padding-top:10px;">	
		<table width="100%" style="margin-left:5%;margin-top:0px;">
			<tr>
				<td width="33%" class="bigfont" style="vertical-align:middle;padding-top:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- Serial No: --> <span class="bigfontset18px"> 
				<?php
				if(isset($serial_number)){
					echo $serial_number;
				}
				?> <span></td>
				<td width="33%" rowspan="2"></td>
				<td> 
					<div style="background: red1;">
						<div class="mb-2" style='width:40px;'><?php echo $barcode_img; ?></div>
						<div class="mb-2" style='width:40px;text-align:center;margin-left: 17px;'>{{@$student->enrollment}}</div>
					</div> 
				</td>
			</tr>
			<tr>
				<td width="33%" style="vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- AI No. --> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset18px"><?php echo $student->ai_code;?></span></td>
				<td style="vertical-align:top;padding-top:5px;"><span class="bigfontset18px"><!-- Revised --> <?php echo $marksheet_type;?><br> <!--Issued On 09-02-2021--><?php echo date('d-m-Y');?></span></td>
			</tr>
		</table>
	</td></tr>	
	
	<tr><td>	
		<table width="100%">
			<tr>
				<td colspan="3"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/></td>
			</tr>
			
			<tr>
				<td colspan="3"></td>
			</tr>
			 
		</table></td>
	</tr>
	<tr>
	<td>
	<table width="100%"><tr><td style="width:90%;">
	<table id="pdetail" width="100%">
	<tr>
	<td style="width:20%;"></td>
	<td class="bigfontset18px" style="padding-top:30px;padding-left:250px;"><span>
	{{@$student->display_exam_month_year}}	
	<!-- APR-MAY 2020 --></span></td>
	
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td class="bigfontset" style="padding:10px 0px 0px 100px;"><span>{{@$student->enrollment}}</span></td>
	</tr>
	<tr>
	<td></td>
	<td class="bigfontset18px" style="padding:10px 0px 0px 300px;"><span style="padding-left:20px;">{{@$student->name}}</span></td>
	</tr>
	<tr>
	<td></td>
	<td class="bigfontset18px" style="padding:0px 0px 0px 250px;"><span>{{@$student->father_name}}</span></td>
	</tr>
	<tr>
	<td></td>
	<td class="bigfontset18px" style="padding:0px 0px 0px 250px;"><span>{{@$student->mother_name}}</span></td>
	</tr>
	<tr>
	<td></td>
	<td class="bigfontset18px" style="padding:10px 0px 0px 370px;"><span>
					{{@$student->dob}}
	</span></td>
	</tr>
	<tr>
	<td></td>
	<td style="padding:10px 0px 0px 330px;"><span><b style="font-size:16px;">{{$dobInWords}}</b></span></td>
	</tr>
	</table></td>
	<td width="10%">
	@if(!empty($documents->photograph))
			<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$student->id.'/'.$documents->photograph)}}" width="60px" />
			@else
				<img alt="materialize logo" height="60px" src="" width="60px" />
			@endif
	</td>
			</tr>
			<tr>
			<td style="height:50px;padding-top:0px;">
	<table style="height:50px;margin-top:10px;">
	<tr>
	
	<td class="bigfontset18px" style="padding:0px 0px 0px 60px;height:25px;">
	{{$student->display_exam_month_year}}<!-- APR-MAY 2020--></td>
	</tr>
	<tr>
	
	<td class="bigfontset18px" style="padding:0px 0px 0px 410px;height:25px;">
	{{$student->display_exam_month_year}}<!-- APR-MAY 2020-->
	</td>
	</tr>
	</table>
	</td>
			</tr>
	</table>
				  </td>
	</tr><tr><td style="height:128px;padding-top:5px;">&nbsp;</td></tr>
	<tr>
	<td class="bigfontset18px" style="height:290px;padding:0px;vertical-align:top;">
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-top:0px;vertical-align:top;width:100%;">
	<tbody>				   
						<?php 
							$r = 1;
							$grandtotalmarks = 0;
								// pr($subjectsPastData);die;
							foreach($examSubjectsMarksData as $key=>$val){
								$rr = "NA";
								if(isset($resultsyntax[$val->final_result])){
									$rr = $resultsyntax[$val->final_result];
								}else{
									$rr = $val->final_result;	
								}
								echo "<tr role='row' class='odd highlight'>";
								echo "<td height='25px;' width='420px'>".$subjects[$val->subject_id]."</td>";
								echo "<td width='85px' style='padding-left:25px;'>";
									
								echo $val->max_marks;
								echo "</td>";
								echo "<td width='85px' style='padding-left:40px;'>";
								if(@$resultsyntax[$val->final_theory_marks])
								{
									echo $resultsyntax[$val->final_theory_marks];
								}else{
									echo $val->final_theory_marks;
								}							
								echo "</td>";
								echo "<td width='65px' style='padding-left:40px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 if(@$resultsyntax[$val->final_practical_marks]){
										 
										 echo $resultsyntax[$val->final_practical_marks];
										 
										 }else{											
											 $subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val->subject_id);
											 
											 if($subjectdetail->practical_type == 0)
											 {
												 echo "";
											 }else{
												 echo $val->final_practical_marks;
											 }
										 }
									 echo "</td>";
									echo "<td width='85px' style='padding-left:40px;'>";
									
									if(@$resultsyntax[$val->sessional_marks_reil_result])
									{echo $resultsyntax[$val->sessional_marks_reil_result];}
									else{echo $val->sessional_marks_reil_result;}
									
									echo "</td>";
									echo "<td width='120px' style='padding-left:10px;'>";
									if(@$resultsyntax[$val->total_marks])
									{echo $resultsyntax[$val->total_marks];}
									else{echo $val->total_marks;}
									echo  "</td>";									
									echo "<td width='230px' style='padding-left:10px;'>";
									if(isset($val->num_words)){ 
										echo $val->num_words; 
									}
									echo "</td>";
									
									echo "<td width='60px' style='padding-left:0px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='60px' style='padding-left:30px;' >";
									if(isset($val->grade_marks)){ 
										echo $val->grade_marks; 
									}
									echo "</td>";
									
								echo "</tr>";
								
									$grandtotalmarks += $val->total_marks;
								
								$r++;
																	
							}?>
							</tbody>					
	</table></td></tr>
	<!--<tr><td style="height:50px;">&nbsp;</td></tr>-->
	<tr><td>
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-left:4%;margin-top:0px;">
			<tr style="font-weight:bold;">
				<td width="30%" style="border:0px;"><!-- Final Result : --> <br><span style="margin-left: 60%;"></span><?php echo (!empty($final_result->final_result)) ?  'PASS': '';?></td>
				 
				<td width="40%" style="border:0px;"><br><span style="margin-left: 80%;"></span> 
				<?php echo (!empty($final_result->total_marks) && $final_result->total_marks != '') ? $final_result->total_marks : '';?>
				</td>
				<td  style="border:0px;"><br><!-- Grand Total -->
				<span style="margin-left: 80%;"></span>
				<?php //if(isset($percentage)){ echo $percentage . "%";}
				 echo (!empty($final_result->percent_marks) && $final_result->percent_marks != '') ? number_format($final_result->percent_marks,2)."%" : '';
				?></td>
			</tr>
		
	  </table>
	</td></tr>
	<tr><td style='padding-top:10px;'>
	<table width="100%" style="text-align:center;">
		<tfoot>
			
			<tr>
				<td width="30%">&nbsp;</td>
				<td width="35%" style="padding-left:30px;"><img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" style="width: 70px;" align="center"/></td>
				<td width="35%" style="padding-left:30px;"><img src="{{asset('public/app-assets/images/favicon/DirectorSign.png')}}" style="width: 70px;" align="center"/></td>
				
				
				<!-- DirectorSign.jpeg -->
			</tr>
			<tr>
				<td width="30%">&nbsp;</td>
				<td width="35%" style="padding-left:30px;"><b>RAJENDRA KUMAR SHARMA</b> <!-- Secratray Sign --></td>
				<td width="35%" style="padding-right:30px;"><b>PRAVEEN KUMAR LEKHARA</b></td> <!-- DirectorSign -->
			</tr>
		</tfoot>
	</table>
	</td></tr>
	<tr><td>
	<table width="100%" style="text-align:center;">
		<tfoot>
			<tr>
				<td width="13%"></td>
				<td width="40%" class="bigfontset" style="padding-top:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$resultDate}}</b> </td>
				<td width="47%">&nbsp;</td>
			</tr>
			
		</tfoot>
	</table>	
	</td></tr>
	</table>
		
		
			
							
			
<?php //echo $this->Form->end();?> 


<?php }else{   ?>


<table width="100%">
<tr>
<td style="height:80px;">	
		<table width="100%" style="margin-left:5%;margin-top:15px;">
			<tr>
				<td width="33%" class="bigfont" style="padding-top:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- Serial No: --> <span class="bigfontset18px"> <?php
				if(isset($serial_number)){
					echo $serial_number;
				}
				?> <span></td>
				<td width="33%" rowspan="2"></td>
				<td> <div style="background: red1;">
				<div class="mb-2" style='width:40px;'><?php echo @$barcode_img; ?></div>
				<div class="mb-2" style='width:40px;text-align:center;margin-left: 17px;'>{{@$student->enrollment}}</div>
				</div>
				</td>
			</tr>
			<tr>
				<td width="33%" style="vertical-align:top;padding-top:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- AI No. --> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset18px"><?php echo $student->ai_code;?></span></td>
				<td style="vertical-align:top;padding-top:5px;"><span class="bigfontset18px"><!-- Revised --> <?php echo $marksheet_type;?><br> <!--Issued On 09-02-2021--><?php echo date('d-m-Y');?></span></td>
			</tr>
		</table>
	</td></tr>	
	<tr><td>	
		<table width="100%">
			<tr>
				<td colspan="3"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/></td>
			</tr>
			
			<tr>
				<td colspan="3"></td>
			</tr>
			 
		</table></td>
	</tr>
	<tr>
		<table width="100%">
			<tr>
				<td width="80%">
				<table id="pdetail2" width="100%">
				<tr>
				<td style="width:20%;"></td>
				<td class="bigfontset18px" style="padding-top:10px;padding-left:250px;"><span>
				{{@$student->display_exam_month_year}}	
				<!-- APR-MAY 2020 --></span></td>
				
				</tr>
				<tr>
				<td>&nbsp;</td>
				<td class="bigfontset" style="height:55px;padding:0px 0px 0px 100px;"><span>{{@$student->enrollment}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px !important;padding:0px 0px 0px 250px;"><span style="padding-left:20px;">{{@$student->name}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 250px;"><span>{{@$student->father_name}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 250px;"><span>{{@$student->mother_name}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 370px;"><span>
								{{@$student->dob}}
				</span></td>
				</tr>
				<tr>
				<td></td>
				<td style="height:40px;padding:0px 0px 0px 330px;"><span><b style="font-size:16px;">{{$dobInWords}}</b></span></td>
				</tr>
				</table>
				</td>
				<td width="10%">
					@if(!empty(@$documents->photograph))
							<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$student->id.'/'.$documents->photograph)}}" width="60px" />
							@else
								<img alt="materialize logo" height="60px" src="" width="60px" />
							@endif
					</td>
			</tr>
		</table>
		
	</tr>
	
	<tr>
	<td class="bigfontset18px" style="padding:0px;vertical-align:top;">
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-top:130px;vertical-align:top;width:100%;">
	<tbody>				   
						<?php 
							$r = 1;
							$grandtotalmarks = 0;
								// pr($subjectsPastData);die;
							foreach(@$examSubjectsMarksData as $key=>$val){
								$rr = "NA";
								if(isset($resultsyntax[$val->final_result])){
									$rr = $resultsyntax[$val->final_result];
								}else{
									$rr = $val->final_result;	
								}
								echo "<tr role='row' class='odd highlight'>";
								echo "<td height='25px;' width='410px'>".$subjects[$val->subject_id]."</td>";
								echo "<td width='85px' style='padding-left:60px;'>";
									
								echo @$val->max_marks;
								echo "</td>";
								echo "<td width='105px' style='padding-left:60px;'>";
								if(@$resultsyntax[@$val->final_theory_marks])
								{
									echo @$resultsyntax[$val->final_theory_marks];
								}else{
									echo @$val->final_theory_marks;
								}							
								echo "</td>";
								echo "<td width='85px' style='padding-left:40px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 if(@$resultsyntax[$val->final_practical_marks]){
										 
										 echo @$resultsyntax[$val->final_practical_marks];
										 
										 }else{											
											 @$subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val->subject_id);
											 
											 if(@$subjectdetail->practical_type == 0)
											 {
												 echo "";
											 }else{
												 echo $val->final_practical_marks;
											 }
										 }
									 echo "</td>";
									echo "<td width='85px' style='padding-left:40px;'>";
									
									if(@$resultsyntax[$val->sessional_marks_reil_result])
									{echo @$resultsyntax[$val->sessional_marks_reil_result];}
									else{echo @$val->sessional_marks_reil_result;}
									
									echo "</td>";
									echo "<td width='105px' style='padding-left:30px;'>";
									if(@$resultsyntax[$val->total_marks])
									{echo @$resultsyntax[$val->total_marks];}
									else{echo @$val->total_marks;}
									echo  "</td>";									
									echo "<td width='190px' style='padding-left:10px;'>";
									if(isset($val->num_words)){ 
										echo $val->num_words; 
									}
									echo "</td>";
									
									echo "<td width='30px' style='padding-left:15px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='60px' style='padding-left:35px;' >";
									if(isset($val->grade_marks)){ 
										echo $val->grade_marks; 
									}
									echo "</td>";
									
								echo "</tr>";
								
									$grandtotalmarks += @$val->total_marks;
								
								$r++;
																	
							}?>
							</tbody>					
	</table></td></tr>
	<!--<tr><td style="height:50px;">&nbsp;</td></tr>-->
	<tr><td>
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-left:4%;margin-top:150px;">
			<tr style="font-weight:bold;">
				<td width="30%" style="border:0px;"><!-- Final Result : --> <br><span style="margin-left: 60%;"></span><?php echo (!empty($final_result->final_result)) ?  'XXXX': '';?></td>
				 
				<td width="40%" style="border:0px;"><br><span style="margin-left: 80%;"></span> 
				<?php echo (!empty($final_result->total_marks) && $final_result->total_marks != '') ? $final_result->total_marks : '';?>
				</td>
				<td  style="border:0px;"><br><!-- Grand Total -->
				<span style="margin-left: 80%;"></span>
				<?php //if(isset($percentage)){ echo $percentage . "%";}
				 //echo (!empty($final_result->percent_marks) && $final_result->percent_marks != '') ? number_format($final_result->percent_marks,2)."%" : '';
				?></td>
			</tr>
		
	  </table>
	</td></tr>
	<tr><td>
	<table width="100%" style="text-align:center;margin:50px 0px 0px 120px;">
		<tfoot>
			
			<tr>
				<td width="40%">&nbsp;</td>
				<td width="30%" style="padding-left:30px;"><img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" style="width: 70px;" align="center"/></td>
				<td width="30%" style="padding-left:30px;"></td>
				
				
				<!-- DirectorSign.jpeg -->
			</tr>
			<tr>
				<td width="40%">&nbsp;</td>
				<td width="30%" style="padding-left:60px;"><b>RAJENDRA KUMAR SHARMA</b> <!-- Secratray Sign --></td>
				<td width="30%" style="padding-right:30px;"><b></b></td> <!-- DirectorSign -->
			</tr>
		</tfoot>
	</table>
	</td></tr>
	<tr><td>
	<table width="100%" style="text-align:center;">
		<tfoot>
			<tr>
				<td width="13%"></td>
				<td width="50%" class="bigfontset" style="padding-top:0px;">&nbsp;<b>{{ @$resultDate }}</b> </td>
				<td width="47%">&nbsp;</td>
			</tr>
			
		</tfoot>
	</table>	
	</td></tr>
	</table>
<?php
 } ?>



<style>
#pdetail tr td{padding:5px 5px 5px 5px;}
#pdetail tr td{height:35px;}
#example4 tr td{padding:5px 5px 5px 5px;}
.additionallabel{margin-top:5px;font-weight:bold;background-color:#3c8dbc;}
.txtRed{
	color: red;
}
.linkalign{
	float:right;
}
.txtGreen{
	color: green;
	font-weight: bold;
	margin-left: 2%;
}

.bigfontset{
	font-size:16px;
	font-weight:bold;
}

.bigfontsetdobwords{
	font-size:14px;
	font-weight:bold;
}
.bigfontset18px{
	font-size:16px;
	font-weight:bold;
	font-family: Arial, Helvetica, sans-serif;
}
.bigfontset18px{
	font-size:16px;
	font-weight:bold;
}
.bigfontset5{
	font-size:18px;
	font-weight:bold;
}
td{font-family: Arial, Helvetica, sans-serif;}
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}

tr.highlight td {padding-top: 5px; padding-bottom:5px}
</style>

