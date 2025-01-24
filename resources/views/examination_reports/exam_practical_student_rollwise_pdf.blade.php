<?php use App\Component\CustomComponent; ?>
<?php use App\Helper\CustomHelper; ?>
<html>
<head>
<style>

.leftTd{
	text-align:left;
	padding-left:10px;
}

.page-break {
	page-break-after: always;
	page-break-inside:avoid;
}

.font{
 font-family: Arial, sans-serif;
 }
</style>
</head>

<body class="font">

<?php $counter = 1; ?>
@foreach($final_data as $key =>$data)

<div style="page-break-inside: avoid">
	<table  cellspacing="0" style="width:100%">
		<tbody>
			<tr>
				<td style="width:10%">
					<p><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px; border-radius: 10px" alt="image upload button"/></p>
				</td>
				<td style="text-align:center;vertical-align:middle;">
					<h2>RAJASTHAN STATE OPEN SCHOOL</h2>
					<h4 style="margin-top:-10px;">SUBJECT WISE PRACTICAL ROLL(Examination,<?php 
										if(isset($exam_session[$stream])){
										echo $exam_session[$stream];
										}?>) 
					</h4>
				</td>
			</tr>
		</tbody>
	</table>
					
	<div class="col-md-12" >
		<table style="width:100%;" class="font">
			<tbody>
				<tr>
					<td width="33%" style="text-align:left;font-size:16px;">Center Code : {{ @$data['cent_code']}} 
					</td>
					<td width="33%" style="text-align:center;font-size:16px;">Subject : {{ $subjectList[$data['subject_id']]}} [Practical]
					</td>
					<td width="33%" style="text-align:right; font-size:16px;">Exam Date : 
						<?php $custom_component_obj = new CustomComponent;
						$exam_date = $custom_component_obj->getExamDate($stream,$data['subject_id']);
						echo $exam_date;
						?>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<span style="width:100% font-size:14px;">Center Name: {{ @$data['cent_name']}}</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
			
	<div class="col-md-12" style="margin-top:25px;">
		<table cellspacing="0"  style="border-top:3px solid black;border-bottom:3px solid black;border-left:1px solid #817373;border-right:1px solid #817373;margin-top: 1%;border-collapse:unset;margin-bottom:0px;" width="100%" class="font">
			<tr class="leftTd">
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">&nbsp; ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
			</tr>
				
			<tr>
								<!-- loop part -->
								<?php
								$enrollment_count = 0;
								if(isset($data['studentData']) && !empty($data['studentData'])){ 
									foreach($data['studentData'] as $studentKey=>$enrollement){
									?>
									@php if ($enrollment_count % 9 == 0){
											echo "</tr><tr>";
									}
									@endphp
					<td style="font-size:16px;padding:5px 5px 5px 5px;">{{ @$enrollement->enrollment }}
					</td>
									<?php $enrollment_count++; 
								} 
								} else { ?>
					<td colspan="10" style="text-align:center">NILL
					</td>
								<?php } ?>
								<!-- loop part -->
			</tr>
		</table>
					
		<table style="width:100%">
				<tr>
					<td colspan='10' width="100%" style="text-align:right"><b>Toal Students : {{ $enrollment_count }}</b>
					</td>
				</tr>
		</table>
					
		<table style="border:0px;">
			<tr>
				<th colspan="10" style="border-bottom: 1px solid !important;border-top: 0px !important;border-right: 0px !important;">Supplementary Students
				</th>
			</tr>
		</table>
						
		<table cellspacing="0" style="border-top: 3px solid black;border-bottom:3px solid black;border-left:1px solid #817373;border-right:1px solid #817373;margin-top: 1%;border-collapse:unset;margin-bottom:0px;" width="100%" class="font">
			<tbody>
							<!-- Supplementary Student part -->
				<tr class="leftTd">
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">&nbsp;ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
					<th style="border-bottom: 1px solid !important;border-right: 0px !important;">ROLL</th>
				</tr>
							
				<tr>
							<?php
								$supp_enrollment_count = 0;
								if(isset($data['suppStudentData']) && !empty($data['suppStudentData'])){ 
									
									foreach($data['suppStudentData'] as $suppStudentKey=>$SuppEnrollement){
										if ($supp_enrollment_count % 9 == 0){
											echo "</tr><tr>";
										}
									?>
					<td style="font-size:16px; padding:5px 5px 5px 5px">{{ @$SuppEnrollement->enrollment }}
					</td>
									<?php $supp_enrollment_count++; 
									} 
								} else { ?>
					<td colspan="10" style="text-align:center">NILL
					</td>
								<?php } ?>  
								<!-- loop part -->
							</tr>
							<!-- Supplementary Student part -->
			</tbody>
		</table>
					
		<table style="width:100%">
			<tbody>
				<tr>
					<td colspan='10' width="100%" style="text-align:right"><b>Total Students : {{ $supp_enrollment_count }}</b>
					</td>
				</tr>
			</tbody>
		</table>
		</div>							
	</div>


<?php $counter++;  
if($counter < ($subject_count+1) ){ ?>
<div style = "display:block; clear:both; page-break-after:always;"></div>
<?php } ?>
@endforeach

</body>
</html>

