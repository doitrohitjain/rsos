<html>
<head>
<style type="text/css">
table.bottomBorder { 
    border-collapse: collapse; 
  }
  table.bottomBorder td, 
  table.bottomBorder th { 
    border-bottom: 1px solid lightgray; 
    padding: 5px; 
    text-align: left;
  }


.font{
 font-family: Arial, sans-serif;
 }
</style>
</head>
 <body class="font">
<table  cellspacing="0" style="width:100%">
		<tr>
			<td style="width:10%">
			<p><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px;  border-radius: 10px" alt="image upload button"/></p>
			</td>
			<td style="text-align:center; vertical-align:middle">
			<p style="font-size:22px;"><b>RAJASTHAN STATE OPEN SCHOOL</b></p>

			<p style="font-size:16px;"><b>AI Code : {{@$reportname}}</b> (EXAMINATION , <?php 
            if(isset($exam_session[$stream])){
              echo $exam_session[$stream];
            }
          ?>)</p>

			<p style="font-size:16px;">AI CENTER NOMINAL ROLL</p>
			</td>
		</tr>
</table>

<?php 
	@$students = $master;


	if(isset($students) && !empty ($students)){  ?>
	<table class="bottomBorder" cellpadding="0" cellspacing="0" style="width:100%">
		<thead>
		<tr>
            <th style="font-size:16px;" colspan="8"><b>Course :&nbsp;<?php 
					if(@$course == 10){
						echo 'SECONDARY';
					}else if(@$course == 12){
						echo 'SENIOR SECONDARY';
					}else{
						echo '-';
					}
				?></b>
			</th>
        </tr>
		<tr>
			<td style="font-size:14px;width:3%;" ><b>Sr.No</b></td>
			<td style="font-size:14px;width:10%;"><b>Enrollment</b></td>
			<td style="font-size:14px;width:18%;"><b>Candidate Name</b></td>
			<td style="font-size:14px;width:15%;"><b>Father Name</b></td>
			<td style="font-size:14px;width:15%;"><b>Mother Name</b></td>
			<td style="font-size:14px;width:14%;"><b>DOB</b></td>
			<td style="font-size:14px;width:5%;"><b>Category</b></td>
			<td style="font-size:14px;width:20%;"><b>Examination Subject</b></td>
		</tr>
		   </thead>
		 	<tbody>
			<?php 
				$showCounter=1;						
					foreach($students as $actualStudent){ 
					$coutnerTemp=0; //echo $courseval;echo '--------';?>
					
					<tr>
						<td style="font-size:14px;width:3%;"><?php echo @$showCounter;?></td>
						<td style="font-size:14px;width:10%;text-transform: uppercase;"><?php $fld ="enrollment"; echo @$actualStudent[$fld]; ?></td>
						<td style="font-size:14px;width:18%;text-transform: uppercase;"><?php $fld ="name"; echo @$actualStudent[$fld];?></td>
						<td style="font-size:14px;width:15%;text-transform: uppercase;"><?php $fld ="father_name"; echo @$actualStudent[$fld];;?></td>
						<td style="font-size:14px;width:15%;text-transform: uppercase;"><?php $fld ="mother_name"; echo @$actualStudent[$fld];;?></td>
						<td style="font-size:14px;width:14%;text-transform: uppercase;"><?php 
							$nyr = substr($actualStudent['enrollment'],-6,-4);
							if(isset($actualStudent['dob']) && @$actualStudent['dob'] != ""){
								if($nyr > 16){
									$sdobarr = explode('-',$actualStudent['dob']);													
									$sdob = $sdobarr[2]."-".$sdobarr[1]."-".$sdobarr[0];
									echo $sdob;
								}else{
									echo @$actualStudent['dob'];
								} 
							}
							?>
						</td>
						<td style="font-size:14px;width:5%;text-transform: uppercase;">
							<?php 
								echo @$categorya[$actualStudent['category_a']];
							?>
						</td >
						<td style="font-size:14px;width:20%;text-transform: uppercase;">
							<?php
							foreach(@$actualStudent['examsubject'] as $key => $value){ 

								echo @$subject_list[$value['subject_id']] . '&nbsp;';
							} ?>
						</td>														
					</tr>
				<?php @$showCounter++;} //foreach actualStudents end ?>
				
				</tbody>
			</table>
						
			<?php }else{ ?>
				<table class="table table-responsive">
					<tr>
						<b>Nill</b>
					</tr>
				</table>
	<?php } ?>
</body>
</html>


