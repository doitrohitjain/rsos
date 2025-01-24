<html>
<head>
<style type="text/css">
.font {
  font-size:10px;

}
.font1 {
  font-size:10px;

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
   thead th {
    word-break:break-all;
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

			<p style="font-size:16px;"><b>AI Code : {{@$reportname}}</b> (EXAMINATION ,March-May {{ now()->year }})</p>

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
			<td class="font" style="font-size:14px;"><b>Sr.No</b></td>
			<td class="font" style="font-size:14px;"><b>Enrollment</b></td>
			<td class="font" style="font-size:14px;"><b>Candidate Name</b></td>
			<td class="font" style="font-size:14px;"><b>Father Name</b></td>
			<td class="font" style="font-size:14px;"><b>Mother Name</b></td>
			<td class="font" style="font-size:14px;"><b>DOB</b></td>
			<td>
			<p class="font" style="font-size:14px;"><b>Category</b></p>
			</td>
			<td class="font" style="font-size:14px;"><b>Examination<br />
			Subject</b>
			</td>
		</tr>
		   </thead>
		 	<tbody>
			<?php 
				$showCounter=1;						
					foreach($students as $actualStudent){ 
					$coutnerTemp=0; //echo $courseval;echo '--------';?>
					 
					<tr>
						<td class="font1"><?php echo @$showCounter;?></td>
						<td class="font1"><?php $fld ="enrollment"; echo @$actualStudent[$fld]; ?></td>
						<td class="font1"><?php $fld ="name"; echo @$actualStudent[$fld];?></td>
						<td class="font1"><?php $fld ="father_name"; echo @$actualStudent[$fld];;?></td>
						<td class="font1"><?php $fld ="mother_name"; echo @$actualStudent[$fld];;?></td>
						<td class="font1"><?php 
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
						<td class="font1">
							<?php 
								echo @$categorya[$actualStudent['category_a']];
							?>
						</td >
						<td class="font1">
							<?php
							foreach(@$actualStudent['exam_subject'] as $key => $value){ 
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


