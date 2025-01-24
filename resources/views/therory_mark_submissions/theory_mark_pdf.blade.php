@php
use App\Component\ThoeryCustomComponent;
@endphp
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
		<style type="text/css">
			table{
				 border-collapse: collapse;
				border-spacing: 0;
				font-family: arial, sans-serif;
			}
			.cc51 {
			  white-space: nowrap;
			  text-align: center;
			  vertical-align: middle;
			}
			.cc55 {
			  display: inline-block;
			  vertical-align: middle;
			}
			.font{
				font-family: Arial, sans-serif;
			}
			#evalhead tr td div{
				text-align: center;

			}
			tr{
			    padding-top:10px;
				border:0.2px solid rgba(2, 2, 2, 0.377);
			}
			
			thead, tfoot { display: table-header-group; }
			tr { page-break-inside: avoid; }
			.new-page {
				page-break-before: always;
			}
			.centerLabel{	
				font-size: 20px;
			}
			fieldset.scheduler-border {
			border: 1px #ccc solid !important;
			padding: 0 1em 1em !important;
			margin: 0 0 0 0 !important;
			-webkit-box-shadow: 0px 0px 0px 0px #000;
			box-shadow: 0px 0px 0px 0px #000;
			}
			legend.scheduler-border {
			font-size: 14px !important;
			font-weight: bold !important;
			text-align: left !important;
			width: auto;
			padding: 0 5px;
			border-top: none;
			border-bottom: none;
			}
			.fieldsetLable-newll {
				color: #0B614B;
				font-weight: bold;
				font-size: 100%;
				font-family: Cambria;
				float: none;
			}
			.pad, .box-title{
			margin-top:10px;
			}
			.page-header {
				padding-bottom:0px !important;
				margin:0px !important;
			}

			label {
				margin-bottom: 0px !important; 
			}
			.text-right {
				text-align: right;
				margin-top: -3%;
				margin-right: 7%;
			}
			.row{
			font-size:16px;
			}
			#signaturetbl tr td{height:25px;}
			h4,h5{text-align:center;}
			/*#TF_Table_Personal th{
			background-color:#ededed;
			}*/
			.bothBorder{ 
				border-bottom: 1px solid #000; 
				border-top: 1px solid #000 !important; 
			}
			.botomBorder{ 
				border-bottom: 2px solid #000 !important; 
			}

		</style>
	</head>
	<body style="margin-top:-5%,margin-bottom:0%">

		<table class="table table-responsive " style="border-left: 5px solid white; border-right:2px solid white;border-top:2px solid white;border-bottom:3px solid black;" width="100%">
			<tbody>
				<tr>
					<th style="width:10%;">
						<h2>
							<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px; height: 60px; border-radius: 10px; font-family: arial, sans-serif;" alt="image upload button"/>
						</h2>
					</th>
					<th style="width:90%;">
						<h2 style="font-family: arial, sans-serif;">RAJASTHAN STATE OPEN SCHOOL</h2>
						<h4 style="font-family: arial, sans-serif">{{$title}}</h4>
					</th>
				</tr>
			</tbody>
		</table>
		<br>
		<table class="table table-responsive" style='width:100%; margin:0%;padding:0%'>
		<tbody>
			<tr>
				<td style="width:14%;padding:10px;"><span><b>Examination Center fixcode</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$examiner_list[@$data->examcenter_detail_id]}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Course</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$course[@$data->course_id]}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Subject</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$subjects[@$data->subject_id]}}</b></span></td>
            </tr>
		
			<tr>
				<td style="width:14%;padding:10px;"><span><b>Maximum Marks</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$getMaxMarks->theory_max_marks}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Examiner</br>SSO</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$userdetails->ssoid}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Examiner Name</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$userdetails->name}}</span></td>
            </tr>

			<tr>
				<td style="width:14%;padding:10px;"><span><b>Total Students Appearing</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$data->total_students_appearing}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Total Copies of the subject</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$data->total_copies_of_subject}}</span></td>
				<td style="width:14%;padding:10px;"><span><b>Total Absent</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$data->total_absent}}</span></td>
			</tr>
			<tr>
				<td style="width:14%;padding:10px;"><span><b>Total</br> NR</b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span>{{@$data->total_nr}}</span></td>	
				<td style="width:14%;padding:10px;"><span><b></b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span><b></b></span></td>				
				<td style="width:14%;padding:10px;"><span><b></b></span></td>
				<td style="width:1%;padding:10px;"><span><b></b></span></td>
				<td style="width:15%;padding:10px;"><span><b></b></span></td>
			</tr>
				</tbody>
		</table>
		<br>
		
		{{-- @dd($result); --}}
		<table class="table " width="100%">
			<tbody>
			<tr><td style='padding:1%'><table class='table table-responsive' width="100%">
				<?php  
				
				if(isset($result) && !empty($result) ) {				
					$i =  0; 
					$colmcounter = 1;
					$nextpagecolmcounter = 0;
					$k=0;
					$pagecounter=1;
					$totalmarks=0;
					foreach($result as $records ) {
					if($i==15)
					{
						$k==0;
						$colmcounter++;
						if($colmcounter==3){
							$colmcounter = 1;
							// echo $i;
						    // echo $k;
							echo "</table></td></tr><tr style='padding:1%'><td style='padding:1%'><table class='table table-responsive '  width='100%' >";
						}else{
							// echo $i;
							// echo $k;
							echo "</table></td><td style='padding:1%'><table class='table table-responsive '  width='100%' >";
						}
					}
					 
					if($i>0 && ($k==30))
					{
						
						$colmcounter++;
						if($colmcounter==3){
							$colmcounter = 1;
							$pagecounter++; 
							echo "</table></td></tr></table><table class='table table-responsive' width='100%' style='page-break-before: always;'><tr><td style='padding:1%;vertical-align:top;'><table class='table table-responsive'  width='100%' style='padding-left:80%;'>";
						}else{ 
							echo "</table></td><td style='padding:1%;vertical-align:top;'><table class='table table-responsive'  width='100%' style='padding-left:80%;'>";
						}
					}
					if($pagecounter > 1 && $i>0 && ($k%20==10))
					{
						$nextpagecolmcounter++;
						if($nextpagecolmcounter==3){							
							$nextpagecolmcounter = 1; 
							echo "</table></td></tr></table><table class='table table-responsive' width='100%' style='page-break-before: always;'><tr><td style='padding:1%;vertical-align:top;max-width:50%;'><table class='table table-responsive'  width='100%' style='padding-left:80%;'>";
						}else{ 
							echo "</table></td><td style='padding:1%;vertical-align:top;'><table class='table table-responsive'  width='100%' style='padding-left:80%;'>";
						}
					}
					
				?>
				<?php if($i==0 || $i==15 || ($k==30) || ($pagecounter > 1 && ($k%20==10))){?>
					<tr>
						<th class="bothBorder" style="width:5%">Sr.No</th>
						<th class="bothBorder" style="width:30%">Fictitious Code</th>
						<th class="bothBorder" style="width:30%">Marks Scored by the student</th>
					</tr>
				<?php } ?>
					<tr>	
						<td style="text-align:center">
							<?php echo $i+1; ?></td>
						<td style="width:10%;text-align:center;padding:0.8%">
							@php 
							echo(isset($records->fixcode))? $records->fixcode:'-'; 
							@endphp
						</td>					
						@if(@$records->theory_absent==1)
							<td style='text-align:center;font-weight:bold;width:100px;height:40px;'>{{strtoupper('Absent')}}</td>
						@elseif(@$records->theory_absent==2)
						<td style='text-align:center;font-weight:bold;width:100px;height:40px;'>{{strtoupper('NR')}}</td>
						
						@else 
							<td style='text-align:center;font-weight:bold;width:100px;height:40px;'>{{$records->final_theory_marks}}</td>
						@php 
							$totalmarks  +=@$records->final_theory_marks;
						@endphp


						@endif
						
					</tr>
				<?php 
					$i++;
					$k++;
				} ?>
				<?php } else { ?>
				<tr>
					<td align="center" style="text-align:center;" colspan="11" > <?php echo __('No Result Found ');?></td>
				</tr>
				<?php } ?>
				</table>
			</tbody>
		</table>
		<table style="clear:both;width:100%;margin-top:150px;">
			<tbody>
				<tr style='border:0.5px solid white '>
					<td align="left" style="width:20%;"><div><b>Date :</b></div> <div>&nbsp;</div></td>
					<td align="right" style="width:33%;"><div><b>Total :</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ $totalmarks}}</div><div style='width:50px;'></div></td>
					<td align="right" style="width:33%;"><div><b>Signature</b></div></td>
				</tr>
				
			</tbody>
		</table>

	

	</body>
</html>


 






