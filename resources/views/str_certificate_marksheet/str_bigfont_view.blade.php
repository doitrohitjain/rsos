<?php use App\Component\CustomComponent; 

	$resultsyntax = array('999' => 'AB','666' => 'SYCP','777' => 'SYCT','888'=>'SYC','P'=>'P');
	
	if(isset($dataSave) && !empty($dataSave)){
	?>	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<table class="" style="width:90%;font-size:10px;margin-left:auto;" >
	<thead>
		<tr>
			<th colspan="2" ><h2 style="font-size:20px;font-weight:bold;text-align:left;width:150px !important;margin-left:2px !important"><b>AI Code:<?php echo @$ai_code; ?></b></h2></th>
			<th colspan="3" ><h2 style="font-size:20px;font-weight:bold;text-align:left;margin-left:2px !important"><b>Result Date: @php echo Config::get('global.result_date');@endphp</b></h2></th>
		</tr>
		<tr>
			<th style="vertical-align: middle;font-size:14px;text-align:center;width:40px !important;margin-left:2px !important">S.No.<BR>Photo</th>
			<th style="font-size:12px;text-align:left;width:110px !important;margin-left:2px !important">Marksheet No.<BR>Roll No.<BR>DOB</th>
			<th style="font-size:12px;text-align:left;width:160px !important;margin-left:2px !important">Candidate Name<BR>Father Name<BR>Mother Name</th>
			<th>
				<table style="width:100%;font-size:12px;border:none !important" class="border-none">
					<tr style="border:none !important">
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-1(-----------MKS-----------)</th>
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-2(-----------MKS-----------)</th>
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-3(-----------MKS-----------)</th>
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-4(-----------MKS-----------)</th>
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-5(-----------MKS-----------)</th>			
					</tr>
					<tr style="border:none !important">
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-6(-----------MKS-----------)</th>
						<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-7(-----------MKS-----------)</th>
						<!--<th style="vertical-align: middle;padding:3px 5px 3px 2px;border:none !important;text-align:left;margin-left:2px !important">SUB-8(-----------MKS-----------)</th>-->
					</tr>
				</table>
			</th>
			
			<th style="vertical-align: middle;font-size:12px;text-align:center;margin-left:2px !important;width:60px !important">Total<BR>Result</th>
		</tr>
	</thead>
	<tbody>
	
	<?php $key = 0; $count = 0;
	// echo count($dataSave);
	// @dd($dataSave);
	foreach(@$dataSave as $value){ 
		
		if(isset($value['enrollment'])){
			@$additional=(isset($value['additional_subjects'])&&!empty($value['additional_subjects']))? unserialize($value['additional_subjects']):"";
			
			$count = $key + 1;
			$file="";
			if(isset($value['id'])){
				$realpath_image = public_path(). DIRECTORY_SEPARATOR  .'documents'. DIRECTORY_SEPARATOR . $value['id'] . DIRECTORY_SEPARATOR . $value['photograph']; 
				$filePath = asset('public/documents'.'/'.$value['id'].'/'.$value['photograph']); 
			}
			?>
				<tr>
					<td style='text-align:center;width:50px'>
					<div style='text-align:center;width:50px !important'>
					<?php  echo $count . ".</div>";
					
						if(!empty(@$realpath_image) && file_exists($realpath_image)) {  ?>
							<img alt="photograph" class="img-rounded" src="<?php echo $filePath; ?>" style="width:30px;">
						<?php 
						} else { ?>
							<img alt="photograph" class="img-rounded" src="{{asset('public/app-assets/images/users1.png')}}" style="width:30px;">
						<?php
						}
						
						/*if(file_exists($filePath)){ // IF THERE IS PHOTOGRAPH FOR CANDIDATE
							$fileData = file_get_contents($filePath);
							$type = pathinfo($filePath, PATHINFO_EXTENSION);
							$base64 = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
							?>
							<img alt="photograph" class="img-rounded" src="<?php echo $base64;?>" style="width:40px;">		
							<?php
						} else {  // // SHOW DEFAULT PHOTOGRAPH
							?>
							<img alt="photograph" class="img-rounded" src="<?php echo $value['photograph'];?>" style="width:40px;">		
							<?php
						}*/
						?>					
					</td>
				
					<td style="font-size:14px;width:120px !important;text-align:left;">
						<?php echo @$value['marksheetno'];?><BR>
						<?php echo @$value['enrollment'];?><BR>
						<?php if(isset($value['dob']) && !empty($value['dob'])){ 
							echo @date('d/m/Y',strtotime($value['dob'])); 
						}?>
					</td>
					
					<td style="font-size:14px;width:160px !important">
						<?php echo ucwords(strtolower(@$value['name']));?><BR>
						<?php echo ucwords(strtolower(@$value['father_name']));?><BR>
						<?php echo ucwords(strtolower(@$value['mother_name']));?>
					</td>
					
					<!-- subject code -->
					<td>
						<table style="width:100%;border:none !important" class="border-none">
							<tr style="border:none !important">
							<?php					
								$internalCount = 0;					
								//FILL THE MARKS HERE
								foreach(@$value['exam_subjects'] as $EsValue){
									if(!empty($additional) && in_array($EsValue['subject_id'],array_keys($additional))){
										continue;											
									}
									$space = "&nbsp;&nbsp;&nbsp;&nbsp;";
									$colmstr = "<td style='vertical-align: middle;font-size:16px;padding:3px 5px 3px 2px;border:none !important'>";
									$colmstr .= @$EsValue['subject_code'] . $space; 
									

									// if (in_array($EsValue['final_theory_marks'], array_keys($resultsyntax))) {
									if(array_key_exists($EsValue['final_theory_marks'], $resultsyntax)){
										$colmstr .= @$resultsyntax[$EsValue['final_theory_marks']];
									} else {
										$colmstr .= @$EsValue['final_theory_marks'];
									}
									$colmstr .= $space;
									
									$customComponentObj = new CustomComponent;
									$subjectdetail = $customComponentObj->subjectDetailById($EsValue['subject_id']);
									if ($subjectdetail->practical_type == 0) {
										   $colmstr .= "-";
									} else {
										// if (in_array($EsValue['final_practical_marks'], array_keys($resultsyntax))) {
										if(array_key_exists($EsValue['final_practical_marks'], $resultsyntax)){
											$colmstr .=  @$resultsyntax[$EsValue['final_practical_marks']];
										} else {
											if($EsValue['final_practical_marks'] > 0){
												$colmstr .= @$EsValue['final_practical_marks'];
											}else{
												$colmstr .= "-";
											}
										}
									}
									$colmstr .= $space;
									
									if(isset($EsValue['sessional_marks_reil_result']) && $EsValue['sessional_marks_reil_result'] > 0 && $EsValue['sessional_marks_reil_result'] != 999){
										$colmstr .= @$EsValue['sessional_marks_reil_result'];
									}else{
										$colmstr .= "-";
									}
									$colmstr .= $space;
									
									// if (in_array($EsValue['total_marks'], array_keys($resultsyntax))) {
									if(array_key_exists($EsValue['total_marks'], $resultsyntax)){
									   $colmstr .=  @$resultsyntax[@$EsValue['total_marks']];
									} else {
										$colmstr .= @$EsValue['total_marks'];
									}
									$colmstr .= $space;
									
									if(isset($resultsyntax[$EsValue['final_result']])){
									   $colmstr .= @$resultsyntax[$EsValue['final_result']];
									} else {
									}
									$colmstr .= $space;
									
									$colmstr .= "</td>";
									echo $colmstr;
									$internalCount++;
								}
								if($internalCount < 4){
									for($i=$internalCount; $i<=5; $i++){
										echo "<td style='border:none !important'>&nbsp;</td>";
									}
								}
								if($internalCount == 5){
									echo "</tr><tr style='border:none !important'>";
								}
								
								foreach($value['exam_subjects'] as $EsValue){
									if(!empty($additional) && in_array($EsValue['subject_id'],array_keys($additional))){
										$space = "&nbsp;&nbsp;&nbsp;&nbsp;";
										$colmstr = "<td style='vertical-align: middle;font-size:16px;padding:3px 5px 3px 2px;border:none !important'>";
										$colmstr .= @$EsValue['subject_code'] . $space; 
										
										// if (in_array($EsValue['final_theory_marks'], array_keys($resultsyntax))) {
										if(array_key_exists($EsValue['final_theory_marks'], $resultsyntax)){
											$colmstr .=  @$resultsyntax[$EsValue['final_theory_marks']];
										} else { 
											if($EsValue['final_theory_marks'] > 0){  
												$colmstr .= $EsValue['final_theory_marks'];
											} else { 
												$colmstr .= "-";
											}
										}
										$colmstr .= $space;
										
										$customComponentObj = new CustomComponent;
										@$subjectdetail = $customComponentObj->subjectDetailById($EsValue['subject_id']);
										if (@$subjectdetail->practical_type == 0) { 
											$colmstr .= "-";
										} else {
											//echo $val['ExamSubject']['final_practical_marks'];
											if (in_array($EsValue['final_practical_marks'], array_keys($resultsyntax))) {
												$colmstr .=  @$resultsyntax[$EsValue['final_practical_marks']];
											} else {
												if($EsValue['final_practical_marks'] > 0){
													$colmstr .= @$EsValue['final_practical_marks'];
												}else{
													$colmstr .= "-";
												}
											}
										}
										$colmstr .= $space;
										
										if(isset($EsValue['sessional_marks_reil_result']) && $EsValue['sessional_marks_reil_result'] > 0 && $EsValue['sessional_marks_reil_result'] != 999){
											$colmstr .= $EsValue['sessional_marks_reil_result'];
										} else {
											$colmstr .= "-";
										}
										$colmstr .= $space;
										
										// if (in_array($EsValue['total_marks'], array_keys($resultsyntax))) {
										if(array_key_exists($EsValue['total_marks'], $resultsyntax)){
										   $colmstr .=  @$resultsyntax[@$EsValue['total_marks']];
										} else {
											$colmstr .= @$EsValue['total_marks'];
										}
										$colmstr .= $space;
										
										if(isset($resultsyntax[@$EsValue['final_result']])){
										   $colmstr .= $resultsyntax[@$EsValue['final_result']];
										} else {
										}
										$colmstr .= $space;
										
										$colmstr .= "</td>";
										echo $colmstr;
										$internalCount++;
									}
								}
								if($internalCount > 4 && $internalCount < 7){
									for($i=$internalCount; $i<=7; $i++){
										echo "<td style='vertical-align: middle;font-size:16px;padding:3px 5px 3px 2px;border:none !important'>&nbsp;</td>";
									}
								}
							?>
							</tr>
						</table>
					</td>
					<!-- subject code -->
					<td style='text-align:center;font-size:14px;width:60px !important'>
						<?php echo @$value['total_marks_enr'];?><BR>
						<?php echo @$value['final_result_enr'];?>
					</td>
			</tr>
			<?php
			}   //END IF CONDITION		
		$key++;
		} //END FOR LOOP
	 } 
?>

	</tbody>
</table>
<style> 
.font{ font-family: Arial; } 
table { 
	border-collapse: collapse; border-spacing: 0; 
} 

table, th, td {
	padding :3px 0px 0px 5px  !important;
	font-family: Arial !important;
	border: 1px solid #c9c2c2 !important;
} 
</style>