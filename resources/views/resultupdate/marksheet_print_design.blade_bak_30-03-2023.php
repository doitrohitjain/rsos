<?php 
use App\Component\ThoeryCustomComponent;
$passfail = null; 

if(isset($final_result['additional_subjects'])&&!empty($final_result['additional_subjects'])){
	$additional=(isset($final_result['additional_subjects'])&&!empty($final_result['additional_subjects']))?unserialize($final_result['additional_subjects']):"";	
}else{
	if(!empty($pastInfo->EX_SUB9) || !empty($pastInfo->FRES6)){
		$additional=array(@$subjectCodeIds[@$pastInfo->EX_SUB9]=>@$pastInfo->FRES6,@$subjectCodeIds[@$pastInfo->EX_SUB7]=>@$pastInfo->FRES7);
	}
}
if(isset($final_result['final_result']) && $final_result['final_result'] != ""){
    $passfail = $final_result['final_result']; 

}

if($passfail == 'pass' || $passfail == 'Pass' || $passfail == "PASS"){ 
?> 
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
					?><span></td>
					<td width="33%" rowspan="2"></td>
					<td> 
						<div style="background: red1;">
							<div class="mb-2" style='width:40px;'><?php echo $barcode_img; ?></div>
							<div class="mb-2" style='width:40px;text-align:center;margin-left: 17px;'> {{@$student['enrollment']}}</div>
						</div> 
					</td>
				</tr>
				<tr>
					<td width="33%" style="vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- AI No. --> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset18px"><?php echo @$student['ai_code'];?></span></td>
					<td style="vertical-align:top;padding-top:5px;"><span class="bigfontset18px"><!-- Revised --> <?php echo $marksheet_type; ?><br> <!--Issued On 09-02-2021--><?php echo date('d-m-Y');?></span></td>
				</tr>
			</table>
		</td>
	</tr>	
	
	<tr>
		<td>	
			<table width="100%">
				<tr>
					<td colspan="3"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/></td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr> 
			</table>
	    </td>
	</tr>
	<tr>
	    <td>
	        <table width="100%"><tr><td style="width:95%;">
				<table id="pdetail" width="100%">
					<tr>
						<td style="width:20%;"></td>
						<td class="bigfontset18px" style="padding-top:20px;padding-left:230px;"><span>
						{{@$student['display_exam_month_year']}}	
					    <!-- APR-MAY 2020 --></span>
					    </td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="bigfontset" style="padding:20px 0px 0px 100px;"><span>{{@$student['enrollment']}}</span></td>
					</tr>
					<tr>
						<td></td>
						<td class="bigfontset18px" style="padding:10px 0px 0px 300px;"><span style="padding-left:20px;">{{@$student['name']}}</span></td>
					</tr>
					<tr>
						<td></td>
						<td class="bigfontset18px" style="padding:0px 0px 0px 250px;"><span>{{@$student['father_name']}}</span></td>
					</tr>
					<tr>
						<td></td>
						<td class="bigfontset18px" style="padding:0px 0px 0px 250px;"><span>{{@$student['mother_name']}}</span></td>
					</tr>
					<tr>
						<td></td>
						<td class="bigfontset18px" style="padding:10px 0px 0px 370px;"><span>
						    {{@$student['dob']}}
					    </span></td>
					</tr>
					<tr>
						<td></td>
						<td style="padding:10px 0px 0px 250px;"><span><b style="font-size:16px;width:100%;">{{$dobInWords}}</b></span></td>
					</tr>
				</table>
			
		</td>
		<td width="10%">
			@if(empty($pastInfo))
			    @php 
				$realpath_image = public_path(). DIRECTORY_SEPARATOR  .'documents'. DIRECTORY_SEPARATOR . $student->id . DIRECTORY_SEPARATOR . $documents->photograph; 
				$photograph_path = asset('public/documents'.'/'.$student->id.'/'.$documents->photograph); 
				
				@endphp
				
				@if(!empty(@$documents->photograph) && file_exists($realpath_image))
				@php  $student_photos =  "data:image/png;base64,".base64_encode(file_get_contents("public/documents/".$student->id.'/'.$documents->photograph));   @endphp
					<img alt="materialize logo" height="60px" src="{{$student_photos}}" width="60px" />
				@else 
					<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/users1.png')}}" width="60px" />
				@endif 
			@else
			   	@php 
					$photograph_physical_path = $pastdatadocument.$student['enrollment'].'.jpg';
					$photograph_path = asset('public/documents/'.$pastdataurl.'/'.$student['enrollment'] . '.jpg');
					
				@endphp
				@if(file_exists($photograph_physical_path))
					<img alt="materialize logo" height="60px" src="{{ $photograph_path }}" width="60px" />
				@else 
					<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/users1.png')}}" width="60px" />
				@endif		
			@endif 
		</td>
	</tr>
	<tr>
	    <td style="height:50px;padding-top:0px;">
	        <table style="height:50px;margin-top:10px;">
				<tr>
					<td class="bigfontset18px" style="padding:0px 0px 0px 40px;height:25px;">
					    {{@$student['display_exam_month_year']}}<!-- APR-MAY 2020-->
					</td>
				</tr>
				<tr>
					<td class="bigfontset18px" style="padding:0px 0px 0px 410px;height:25px;">
					    {{@$student['display_exam_month_year']}}<!-- APR-MAY 2020-->
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
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin:0px;vertical-align:top;width:100%;">
	<tbody>				   
						<?php 
							$r = 1;
							$grandtotalmarks = 0;
								// dd($examSubjectsMarksData);die;
							foreach($examSubjectsMarksData as $key=>$val){
								// dd($val->subject_id);
								if(!empty($additional) && in_array($val['subject_id'],array_keys($additional))){
											continue;											
										}
								$rr = "NA";
								if(isset($resultsyntax[$val['final_result']])){
									$rr = $resultsyntax[$val['final_result']];
								}else{
									$rr = $val['final_result'];	
								}
								echo "<tr role='row' class='odd highlight' >";
								echo "<td style='' height='25px;' width='530px' >".$subjects[$val['subject_id']]."</td>";
								echo "<td width='95px' style='padding-left:25px;'>";
									
								echo $val['max_marks'];
								echo "</td>";
								echo "<td width='100px' style='padding-left:20px;'>";
								if(@$resultsyntax[$val['final_theory_marks']])
								{
									echo $resultsyntax[$val['final_theory_marks']];
								}else{
									echo $val['final_theory_marks'];
								}							
								echo "</td>";
								echo "<td width='80px' style='padding-left:20px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 $subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val['subject_id']);
									 if(@$resultsyntax[$val['final_practical_marks']] && $subjectdetail->practical_type  == 1 ){
										 echo $resultsyntax[$val['final_practical_marks']];
										}else{											
											if($subjectdetail->practical_type == 0)
											{
												 echo "";
											}else{
												 echo $val['final_practical_marks'];
											}
										 }
									 echo "</td>";
									echo "<td width='90px' style='padding-left:40px;'>";
										
									if(@$resultsyntax[$val['sessional_marks_reil_result']])
									{ echo $resultsyntax[$val['sessional_marks_reil_result']]; }
									else{echo $val['sessional_marks_reil_result'];}
									
									echo "</td>";
									echo "<td width='130px' style='padding-right:0px;text-align:center;'>";
									if(@$resultsyntax[$val['total_marks']])
									{echo $resultsyntax[$val['total_marks']];}
									else{echo $val['total_marks'];}
									echo  "</td>";									
									echo "<td width='260px' style='padding-left:0px;'>";
									if(isset($val['num_words'])){ 
										echo $val['num_words']; 
									}
									echo "</td>";
									
									echo "<td width='80px' style='padding-left:0px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='30px' style='padding-left:10px;' >";
									if(isset($val['grade_marks'])){ 
										echo $val['grade_marks']; 
									}
									echo "</td>";
									
								echo "</tr>";
								$grandtotalmarks += (int)$val['total_marks'];
								
								$r++;
																	
							}
							$k=0;
							foreach($examSubjectsMarksData as $key=>$val){
								if(!empty($additional) && in_array($val['subject_id'],array_keys($additional)))
										{
								$rr = "NA";
								if($k==0){
										echo "<tr ><td colspan=9 style='text-decoration:underline;'>Additional</td></tr>";
								}
								if(isset($resultsyntax[$val['final_result']])){
									$rr = $resultsyntax[$val['final_result']];
								}else{
									$rr = $val['final_result'];	
								}
								echo "<tr role='row' class='odd highlight' style=';'>";
								echo "<td  style='' height='25px;' width='530px'>".$subjects[$val['subject_id']]."</td>";
								echo "<td width='95px' style='padding-left:25px;'>";
									
								echo $val['max_marks'];
								echo "</td>";
								echo "<td width='100px' style='padding-left:20px; '>";
								if(@$resultsyntax[$val['final_theory_marks']])
								{
									echo $resultsyntax[$val['final_theory_marks']];
								}else{
									echo $val['final_theory_marks'];
								}							
								echo "</td>";
								echo "<td width='80px' style='padding-left:20px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 $subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val['subject_id']);
									 if(@$resultsyntax[$val['final_practical_marks']] && $subjectdetail->practical_type  == 1 ){
										 echo $resultsyntax[$val['final_practical_marks']];
										}else{											
											if($subjectdetail->practical_type == 0)
											{
												 echo "";
											}else{
												 echo $val['final_practical_marks'];
											}
										 }
									 echo "</td>";
									echo "<td width='90px' style='padding-left:40px;'>";
									
									if(@$resultsyntax[$val['sessional_marks_reil_result']])
									{echo $resultsyntax[$val['sessional_marks_reil_result']];}
									else{echo $val['sessional_marks_reil_result'];}
									
									echo "</td>";
									echo "<td width='130px' style='padding-right:0px;text-align:center;'>";
									if(@$resultsyntax[$val['total_marks']])
									{echo $resultsyntax[$val['total_marks']];}
									else{echo $val['total_marks'];}
									echo  "</td>";									
									echo "<td width='260px' style='padding-left:0px;'>";
									if(isset($val['num_words'])){ 
										echo $val['num_words']; 
									}
									echo "</td>";
									
									echo "<td width='80px' style='padding-left:0px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='30px' style='padding-left:10px;' >";
									if(isset($val['grade_marks'])){ 
										echo $val['grade_marks']; 
									}
									echo "</td>";
									
								echo "</tr>";
								
									$grandtotalmarks += (int)$val['total_marks'];
								$k++;
								$r++;
							}else{
										continue;
							}								
							}
							?>
							</tbody>					
	</table></td></tr>
	<!--<tr><td style="height:50px;">&nbsp;</td></tr>-->
	<tr><td>
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-left:4%;margin-top:0px;">
			<tr style="font-weight:bold;">
				<td width="30%" style="border:0px;"><!-- Final Result : --> <br><span style="margin-left: 60%;"></span><?php echo (!empty($final_result['final_result'])) ?  'PASS': '';?></td>
				 
				<td width="40%" style="border:0px;"><br><span style="margin-left: 80%;"></span> 
				<?php echo (!empty($final_result['total_marks']) && $final_result['total_marks'] != '') ? $final_result['total_marks'] : '';?>
				</td>
				<td  style="border:0px;"><br><!-- Grand Total -->
				<span style="margin-left: 80%;"></span>
				<?php //if(isset($percentage)){ echo $percentage . "%";}
				 echo (!empty($final_result['percent_marks']) && $final_result['percent_marks'] != '') ? number_format($final_result['percent_marks'],2)."%" : '';
				?></td>
			</tr>
		
	  </table>
	</td></tr>
	<tr><td style='padding-top:10px;'>
	<table width="100%" style="text-align:center;">
		<tfoot>
			
			<tr>
				<td width="30%">&nbsp;</td>
				<td width="35%" style="padding-left:30px;">
				<?php 
					$secratory_sign = "data:image/png;base64,".base64_encode(file_get_contents("public/app-assets/images/favicon/signatureinpdf1.png"));
					$director_sign =  "data:image/png;base64,".base64_encode(file_get_contents("public/app-assets/images/favicon/DirectorSign1.png"));
				?>
				<img src="{{$secratory_sign}}" style="width: 70px;" align="center"/></td>
				<td width="35%" style="padding-left:30px;"><img src="{{$director_sign}}" style="width: 70px;" align="center"/></td>
				
				
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
				<td width="40%" class="bigfontset" style="padding-top:10px;">&nbsp;&nbsp;<b>{{$resultDate}}</b> </td>
				<td width="47%">&nbsp;</td>
			</tr>
			
		</tfoot>
	</table>	
	</td></tr>
	</table>
		
		
			
							
			
<?php //echo $this->Form->end();?> 


<?php }else{  
	// fail marksheets 
	 ?>

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
				<div class="mb-2" style='width:40px;text-align:center;margin-left: 17px;'>{{@$student['enrollment']}}</div>
				</div>
				</td>
			</tr>
			<tr>
				<td width="33%" style="vertical-align:top;padding-top:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- AI No. --> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset18px"><?php echo $student['ai_code'];?></span></td>
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
				<td class="bigfontset18px" style="padding-top:0px;padding-left:250px;"><span>
				{{@$student['display_exam_month_year']}}	
				<!-- APR-MAY 2020 --></span></td>
				
				</tr>
				<tr>
				<td>&nbsp;</td>
				<td class="bigfontset" style="height:55px;padding:0px 0px 0px 100px;"><span>{{@$student['enrollment']}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px !important;padding:0px 0px 0px 250px;"><span style="padding-left:20px;">{{@$student['name']}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 250px;"><span>{{@$student['father_name']}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 250px;"><span>{{@$student['mother_name']}}</span></td>
				</tr>
				<tr>
				<td></td>
				<td class="bigfontset18px" style="height:55px;padding:0px 0px 0px 370px;"><span>
								{{@$student['dob']}}
				</span></td>
				</tr>
				<tr>
				<td></td>
				<td style="height:40px;padding:0px 0px 0px 330px;"><span><b style="font-size:16px;">{{$dobInWords}}</b></span></td>
				</tr>
				</table>
				</td>
				<td width="10%">
					@if(empty($pastInfo))
						@php 
						$realpath_image = public_path(). DIRECTORY_SEPARATOR  .'documents'. DIRECTORY_SEPARATOR . $student->id . DIRECTORY_SEPARATOR . $documents->photograph; 
						$photograph_path = asset('public/documents'.'/'.$student->id.'/'.$documents->photograph); 
						@endphp
						@if(!empty(@$documents->photograph) && file_exists($realpath_image))
							<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$student->id.'/'.$documents->photograph)}}" width="60px" />
						@else 
							<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/users1.png')}}" width="60px" />
						@endif 
			        @else
						@php 
							$photograph_physical_path = $pastdatadocument.$student['enrollment'].'.jpg';
							$photograph_path = asset('public/documents/'.$pastdataurl.'/'.$student['enrollment'] . '.jpg');
						@endphp
						@if(file_exists($photograph_physical_path))
							<img alt="materialize logo" height="60px" src="{{ $photograph_path }}" width="60px" />
						@else 
							<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/users1.png')}}" width="60px" />
						@endif		
					@endif  
					</td>
			</tr>
		</table>
		
	</tr>
	
	<tr>
	<td class="bigfontset18px" style="padding:0px;vertical-align:top;">
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-top:110px;vertical-align:top;width:100%;">
	<tbody style=''>				   
						<?php 
							$r = 1;
							$grandtotalmarks = 0;
								// pr($subjectsPastData);die;
							foreach(@$examSubjectsMarksData as $key=>$val){
								if(!empty($additional) && in_array($val['subject_id'],array_keys($additional))){
									continue;											
								}
								$rr = "NA";
								if(isset($resultsyntax[$val['final_result']])){
									$rr = $resultsyntax[$val['final_result']];
								}else{
									$rr = $val['final_result'];	
								}
								echo "<tr role='row' class='odd highlight'>";
								echo "<td height='25px;' width='550px'>".$subjects[$val['subject_id']]."</td>";
								echo "<td width='95px' style='padding-left:25px;'>";
								echo $val['max_marks'];
								echo "</td>";
								echo "<td width='100px' style='padding-left:20px;'>";
								if(@$resultsyntax[$val['final_theory_marks']])
								{ 
									echo @$resultsyntax[$val['final_theory_marks']];
								}else{

									echo @$val['final_theory_marks'];
								}							
								echo "</td>";
								echo "<td width='80px' style='padding-left:20px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 $subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val['subject_id']);
									 if(@$resultsyntax[$val['final_practical_marks']] && $subjectdetail->practical_type  == 1 ){
										 echo $resultsyntax[$val['final_practical_marks']];
										}else{											
											if($subjectdetail->practical_type == 0)
											{
												 echo "";
											}else{
												 echo $val['final_practical_marks'];
											}
										 }
									 echo "</td>";
									echo "<td width='90px' style='padding-left:40px;'>";
									
									if(@$resultsyntax[$val['sessional_marks_reil_result']])
									{echo $resultsyntax[$val['sessional_marks_reil_result']];}
									else{echo @$val['sessional_marks_reil_result'];}
									
									echo "</td>";
									echo "<td width='110px' style='padding-right:10px;'>";
									if(@$resultsyntax[$val['total_marks']])
									{echo @$resultsyntax[$val['total_marks']];}
									else{echo @$val['total_marks'];}
									echo  "</td>";									
									echo "<td width='260px' style='padding-left:10px;'>";
									if(isset($val['num_words'])){ 
										echo $val['num_words']; 
									}
									echo "</td>";
									
									echo "<td width='80px' style='padding-left:10px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='30px' style='padding-left:10px;' >";
									if(isset($val['grade_marks'])){ 
										echo $val['grade_marks']; 
									}
									echo "</td>";
									
								echo "</tr>";
								$grandtotalmarks += (int)$val['total_marks'];
								$r++;
																	
							}
							$k=0;
							foreach(@$examSubjectsMarksData as $key=>$val){
								if(!empty($additional) && in_array($val['subject_id'],array_keys($additional))){
								
								$rr = "NA";
								if($k==0){
										echo "<tr><td colspan=9 style='text-decoration:underline;'>Additional</td></tr>";
								}
								if(isset($resultsyntax[$val['final_result']])){
									$rr = $resultsyntax[$val['final_result']];
								}else{
									$rr = $val['final_result'];	
								}
								echo "<tr role='row' class='odd highlight'>";
								echo "<td height='25px;' width='550px'>".$subjects[$val['subject_id']]."</td>";
								echo "<td width='95px' style='padding-left:25px;'>";
									
								echo $val['max_marks'];
								echo "</td>";
								echo "<td width='100px' style='padding-left:20px;'>";
								if(isset($resultsyntax[$val['final_theory_marks']]))
								{
									echo @$resultsyntax[$val['final_theory_marks']];
								}else{
									echo @$val['final_theory_marks'];
								}							
								echo "</td>";
								echo "<td width='80px' style='padding-left:20px;'>";
									
									 //echo $val['ExamSubject']['final_practical_marks'].
									 $subjectdetail = ThoeryCustomComponent::_getSubjectDetail($val['subject_id']);
									 if(@$resultsyntax[$val['final_practical_marks']] && $subjectdetail->practical_type  == 1 ){
										 echo $resultsyntax[$val['final_practical_marks']];
										}else{											
											if($subjectdetail->practical_type == 0)
											{
												 echo "";
											}else{
												 echo $val['final_practical_marks'];
											}
										 }
									 echo "</td>";
									echo "<td width='90px' style='padding-left:40px;'>";
									if(@$resultsyntax[$val['sessional_marks_reil_result']])
									{echo @$resultsyntax[$val['sessional_marks_reil_result']];}
									else{echo @$val['sessional_marks_reil_result'];}
									
									echo "</td>";
									echo "<td width='110px' style='padding-right:10px;'>";
									if(@$resultsyntax[$val['total_marks']])
									{echo $resultsyntax[$val['total_marks']];}
									else{echo @$val['total_marks'];}
									echo  "</td>";									
									echo "<td width='260px' style='padding-left:10px;'>";
									if(isset($val['num_words'])){ 
										echo $val['num_words']; 
									}
									echo "</td>";
									
									echo "<td width='80px' style='padding-left:10px;'>";
									if($rr != ""){
										echo $rr;										
									}else{
										//echo "Test";
									}
									echo "</td>";
									echo "<td width='30px' style='padding-left:10px;' >";
									if(isset($val['grade_marks'])){ 
										echo $val['grade_marks']; 
									}
									echo "</td>";
									
								echo "</tr>";
								
								$grandtotalmarks += (int)$val['total_marks'];
								
								$r++;
								$k++;
																	
							}else{
										continue;
							}
						}


							?>
							</tbody>					
	</table></td></tr>
	<!--<tr><td style="height:50px;">&nbsp;</td></tr>-->
	<tr><td>
	<table id="example4" class="table2 table-bordered2 bigfontset18px" role="grid2" width="92%" style="margin-left:4%;margin-top:145px;">
			<tr style="font-weight:bold;">
				<td width="30%" style="border:0px;"><!-- Final Result : --> <br><span style="margin-left: 60%;"></span><?php echo (!empty($final_result['final_result'])) ?  'XXXX': '';?></td>
				 
				<td width="40%" style="border:0px;"><br><span style="margin-left: 80%;"></span> 
				<?php echo (!empty($final_result['total_marks']) && $final_result['total_marks'] != '') ? $final_result['total_marks'] : '';?>
				</td>
				<td  style="border:0px;"><br><!-- Grand Total -->
				<span style="margin-left: 80%;"></span>
				<?php //if(isset($percentage)){ echo $percentage . "%";}
				// echo (!empty($final_result->percent_marks) && $final_result->percent_marks != '') ? number_format($final_result->percent_marks,2)."%" : '';
				?></td>
			</tr>
		
	  </table>
	</td></tr>
	<tr><td>
	<table width="100%" style="text-align:center;margin:50px 0px 0px 80px;">
		<tfoot>
			
			<tr>
				<td width="50%">&nbsp;</td>
				<td width="50%" style="padding-left:0px;text-align:left;"><img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" style="width: 70px;" align="center"/></td>
				{{-- <td width="35%" style="padding-left:30px;"><img src="{{asset('public/app-assets/images/favicon/DirectorSign.png')}}" style="width: 70px;" align="center"/></td> --}}
				
				
				
				<!-- DirectorSign.jpeg -->
			</tr>
			<tr>
				<td width="50%">&nbsp;</td>
				<td width="50%" style="padding-left:0px;text-align:left;"><b>RAJENDRA KUMAR SHARMA</b> <!-- Secratray Sign --></td>
				{{-- <td width="30%" style="padding-right:30px;"><b>PRAVEEN KUMAR LEKHARA</b></td> <!-- DirectorSign --> --}}
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


th {
  border: 1px solid black;
}


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

