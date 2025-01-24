<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
		<style type="text/css">
			.tblCss {
				font-family: arial, sans-serif;
				border-collapse:collapse;
                border-spacing:0 15px;
				width:100%;
				border-left :0px;
			}
			.tr_style{
				border-collapse: collapse;
				border-spacing: 1;
				font-family: arial, sans-serif;
				border-top: 1px solid #E1DEDD;
				border-bottom: 1px solid #E1DEDD;
				border-left: 0px;
				border-right: 0px;
				padding-bottom: 10px;
				height:33px;
			}
			.td_style{
				border-collapse: collapse;
				border-spacing: 1;
				font-family: arial, sans-serif;
				border: none;
			}
			.cc51 {
			  white-space: nowrap;
			  vertical-align: middle;
			}
			.cc55 {
			  display: inline-block;
			  vertical-align: middle;
			}
			.font{
				font-family: Arial, sans-serif;
				border-color:#E1DEDD;
				font-size:12px;
			}
			.TextAlignLeft {
				text-align:left;
			}
			.textAlignCenter {
				text-align:center;
			}
			.bottomBorderNone {
				border:0px !important;
			}
			.signTbl {
				margin-top:20px;
			}
			.bodyCss {
				margin-top:-5%,margin-bottom:0%;
			}
			.logoSubHeadingCss {
				font-weight:bold;
				margin-left:37%;
			}
			.logoDivCss {
				border-top:1px solid #E1DEDD;
				border-bottom:1px solid #E1DEDD;
				margin-bottom:15px;
			}
			.logoSubDivCss {
				width:30%;
				margin-left:30%
			}
			.logoCss {
				width: 50px; height: 40px; border-radius: 10px;margin-top:5px;
			}
			.logoTextCss {
				font-size:16px;margin-top:5px;
			}
			.nameSignMargin {
				margin-left:70%;
			}
		</style>
	</head>
	
	<body  class="font bodyCss" >
		<div class="cc51 textAlignCenter">
			<div class="cc51 textAlignCenter logoDivCss">
				<div class="logoSubDivCss">
					<div class="cc55">
						<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" class="logoCss" alt="image upload button"/>
					</div> 
					<span class="font textAlignCenter logoTextCss"><b>RAJASTHAN STATE OPEN SCHOOL JAIPUR</b></span>
				</div>
			</div>
		</div>
		
		<table class="font tblCss">
			<tr class="tr_style">
				<td class="td_style" colspan="6"><span class="logoSubHeadingCss">RSOS  EXAM  <?php echo $current_practical_session; ?></span></td>
			</tr>
			<tr class="tr_style">
				<th class="td_style TextAlignLeft">SUBJECT</th>
				<td class="td_style TextAlignLeft">{{ @$subject_list[@$examinerMapData['subject_id']] }}</td>
				<th class="td_style TextAlignLeft">Max Marks</th>
				<td class="td_style TextAlignLeft">{{ @$subjectMaxMarks }}</td>
			</tr>
			<tr class="tr_style">	
				<th class="td_style TextAlignLeft">Examiner SSO ID</th>
				<td class="td_style TextAlignLeft">{{ @$practical_examiner_sso_id }}</td>
				
				<th class="td_style TextAlignLeft">Examiner NAME</th>
				<td class="td_style TextAlignLeft" colspan="3">{{ @$practical_examiner_name }}</td>
			</tr>
			<tr class="tr_style">
				<th class="td_style TextAlignLeft">EXAM CENTER CODE</th>
				<td class="td_style TextAlignLeft">
					<?php 
					if(@$examinerMapData->course==10){
						echo @$examcenterDetailData->ecenter10;
					} else if(@$examinerMapData->course==12){ 
						echo @$examcenterDetailData->ecenter12;
					} else {
						echo "-";
					} ?>
				</td>
				<th class="td_style TextAlignLeft">EXAM CENTER NAME</th>
				<td class="td_style TextAlignLeft" colspan="3">{{ @$examcenter_list[@$examinerMapData['examcenter_detail_id']] }}</td>
			</tr>
			<tr class="tr_style">	
				<th class="td_style TextAlignLeft">LOCK AND SUBMIT DATETIME</th>
				<td class="td_style TextAlignLeft">{{ @$examinerMapData['practical_lastpage_submitted_date'] }}</td>
				<th class="td_style TextAlignLeft">PRINT DATE TIME:</th>
				<td class="td_style TextAlignLeft" colspan="3">{{ date("Y-m-d H:m:i") }}</td>
			</tr>
		</table>
		</br></br>
		
		<table class="font tblCss">
			<thead>
				<tr class="tr_style">
					<th class="td_style TextAlignLeft" style="width:10%">Sr.No.</th>
					<th class="td_style TextAlignLeft" style="width:40%">Name</th>
					<th class="td_style TextAlignLeft" style="width:20%">Enrollment</th>
					<th class="td_style TextAlignLeft" style="width:10%">Is Absent</th>
					<th class="td_style TextAlignLeft" style="width:20%">Final Marks</th>
				</tr>
			</thead>	
			<tbody>
				<?php 
				if(!empty($master)){ 
				$i = 1;
				$no_present = 0;
				$no_absent = 0;
				foreach($master as $data) { 
				?>
				<tr class="tr_style">
					<td class="td_style TextAlignLeft" style="width:10%"> &nbsp;{{ @$i }}</td>
					<td class="td_style TextAlignLeft" style="width:40%"> &nbsp;{{ @$data->name }}</td>
					<td class="td_style TextAlignLeft" style="width:20%"> &nbsp;{{ @$data->enrollment }}</td>
					<td class="td_style TextAlignLeft" style="width:10%">
						<?php
							$is_absent_present_title = '-';
							if(@$data->practical_absent=='0'){
								$is_absent_present_title = 'Present';
								$no_present++;
							} else {
								$no_absent++;
								$is_absent_present_title = 'Absent';
							}
							echo $is_absent_present_title;
						?>
					</td>
					<td class="td_style TextAlignLeft" style="width:20%">
					<?php 
					if(isset($data->final_practical_marks) && !empty($data->final_practical_marks) && @$data->practical_absent!=1) { echo $data->final_practical_marks; } else {
						if($data->final_practical_marks == 0){
							echo "0"; 
						}else{
							echo "-"; 
						}
					
					}?></td>
				</tr>
				<?php $i++; } } ?>
				
				<tr class="tr_style">
					<td class="td_style" colspan="6"><b>NO OF PRESENT :</b> {{ @$no_present; }}</td>
				</tr>
				<tr class="tr_style">
					<td class="td_style" colspan="6"><b>NO OF ABSENT :</b> {{ @$no_absent; }}</td>
				</tr>
				<tr class="tr_style">
					<td class="td_style" colspan="6"><span class="nameSignMargin">{{ @$practical_examiner_name }}</span></td>
				</tr>
			</tbody>	
		</table>
		
		<table class="font tblCss signTbl">
			<tr style="margin-top:20%" class="tr_style bottomBorderNone">
				<td class="td_style" colspan="6"><span class="nameSignMargin"><b>EXAMINER SIGNATURE</b><span></td>
			</tr>
		</table>
		
	</body>
</html>


 



