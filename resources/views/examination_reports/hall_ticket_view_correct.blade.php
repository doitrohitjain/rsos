<meta http-equiv="content-type" content="text/html; charset=utf-8" />
@php 
	use App\Helper\CustomHelper;
	@endphp 
<section class="invoice">
	@php
	if(isset($students) && !empty($students)){
		foreach($students as $courseval => $studentCourses){
			$counter = 1;$hallticketcounter = 1;
			
				foreach($studentCourses as $skey => $student){ 
				//echo $ai_code; echo '--------';
				// pr($ai_code);										
				//pr($student->enrollment);die; 
		@endphp
				<div class='admitCard'>
					<div class="row" style="border-style: groove;margin-top:10px;margin-left:5px;margin-right:5px;">
						<div class="col-xs-12 hallticketbackcss">
							<div class="row">
								<div class="col-xs-1" >
									<h2>
										<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 50px; height: 40px; border-radius: 10px" alt="image upload button"/>
									</h2>
								</div>
								<div class="col-xs-10" > 
									<div class="col-xs-12" >
										<h2 style="font-size:22px;font-weight:bold;">
											RAJASTHAN STATE OPEN SCHOOL,JAIPUR
										</h2>
									</div>							
									<div class="col-xs-12" style="margin-top:0px !important;">
									<h4  style="margin-top:0px !important;"> 
										<?php echo @$examDates[1];
if ($student['course'] == 10)
{ ?>
											Secondary
										<?php
}
else if ($student['course'] == 12)
{ ?>
											Senior Secondary
										<?php
} ?>Examination, (Hall Ticket) </h4>
									</div>
								</div>
							</div>
						</div> 
						<div class="col-md-12" > 
						<div class="col-xs-12">
							<div class="row">
							<table style="width:100%;"><tr><td style="width:70%;">								
									<?php
if (isset($student['exam_subjects']['T']) && !empty($student['exam_subjects']['T']))
{ ?>
										<table class="table2" role="grid" aria-describedby="example2_info" style="width:100%;font-size:12px;">
											<tbody>
												<tr>
													<td colspan=4><h4 style="font-size:10px;font-weight:bold;text-transform: uppercase;"> CENTRE:
													<?php echo ($student['course'] == 10) ? $student['ecenter10'] : $student['ecenter12']; ?>-<?php echo ucwords($student['cent_name']); ?><?php //echo ucwords($student['cent_add1']);
     ?><?php //echo ucwords($student['cent_add2']);
     ?></h4> </td>
												</tr>
												<tr>
													<th style="padding:5px;">Code</th>
													<th style="padding:5px;">Subject[T:Theory,P:Practical]</th>
													<th style="padding:5px;">Date</th>
													<th style="padding:5px;">Exam.Time</th>
												</tr>
												<?php

    foreach (@$student['exam_subjects']['T'] as $key => $subject)
    { 
        $suppFlag = false;
        $result = CustomHelper::getStudentResult($student['enrollment'], $subject['id']);
	 
        if (in_array($subject['id'], $practicalsubjects12) && $student->type == 'Supplementary' && $result == 777){
            $suppFlag = true;
        }
		
		
        $paperCodes = array();
        if ((@$student['exam_subjects']['T'][$key]) && $result != 666)
        {
            $paperCodes[] = 'T';
        }
        if ((@$student['exam_subjects']['P'][$key]) && $suppFlag === false )
        {
            $paperCodes[] = 'P';
        }
        $strpapercode = '[' . join(',', array_values($paperCodes)) . ']';

		 
        
?>
													<tr>
														<td style="padding:5px;"><?php echo $key; ?></td>
														<td style="padding:5px;"><?php echo $subject['name'] . ' ' . $strpapercode; ?></td>
														<td style="padding:5px;">
														
															<?php
        if ($result != 666)
        {
            echo date('d-m-Y', strtotime($subject['exam_date']));
        }
?>
														</td>
														<td style="padding:5px;">														
															<?php
        if ($result != 666)
        {
            echo $subject['exam_time_start']; ?> TO <?php echo $subject['exam_time_end'];
        }
?>
														</td>
													</tr>
												<?php
    } ?>
											</tbody>
										</table>
									<?php
} ?>
							
								</td><td style="width:30%;">
								
									<table id="example2" class="table2" role="grid" aria-describedby="example2_info" style="width:100%;padding:25px;">
										<tbody>
											<tr>
												<td>
													<div>
														<?php  
															$studentDocumentPath = $studentDocumentPath. $student['student_id'];
															$fld = 'photograph';$lbl = __('Photograph');$path = $student[$fld];
														?>
														 
														<span>
															<img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.$path)}}" width="50px" />
														</span> 
													</div>
											
													<div>
														<?php  
															$fld = 'signature';$lbl = __('Signature');$path = $student[$fld]; 
														?>  
														<span>
															<img alt="materialize logo" height="50px" src="{{asset('public/'.$studentDocumentPath.'/'.$path)}}" width="50px" />
														</span> 
													</div>
												</td> 
												<td><table style="padding:25px;">
												<tr>
												<td>
													<h4 style="font-size:22px;font-weight:bold;">
														<b>Roll No.: {{$student['enrollment']}}
														</b>
													</h4>
												</td>
												</tr>
												<tr>	
													<td class="leftTd" style="padding:5px;">
														Name.: {{$student['name']}}							
													</td>
												</tr>
												<tr>	
													<td class="leftTd" style="padding:5px;">
														Father Name{{$student['father_name']}}	
																					
													</td>
												</tr>
												<?php if (isset($student['tehsil']) && $student['tehsil'] != '')
{ ?>
													<tr>	
														<td class="leftTd" style="padding:5px;">
															Block/Teh. : {{$student['tehsil']}}	
														</td>
													</tr>
												<?php
} ?>							
												<?php if (isset($student['district']) && $student['district'] != '')
{ ?>
													<tr>	
														<td class="leftTd" style="padding:5px;">
															Distt. : {{$student['district']}}								
														</td>
													</tr>
												<?php
} ?>							
												
												<?php if (isset($student['dob']) && $student['dob'] != "")
{ ?>
												<tr>	
													<td class="leftTd"style="padding:5px;">
														DOB. : {{$student['dob']}}
													</td>
												</tr>
												<?php
} ?>							
												</table></td>
											</tr>
											<!--<tr>
												<td class="leftTd" colspan=2>
													<?php
//echo "<span><b>Online Application Ref.No.: ".h($student->enrollment) . '</b></span>';

?>							
												</td>
											</tr>-->
										</tbody>
									</table>
								
								</td></tr></table>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12" >
									<h5 class="page-header2" style="font-weight:bold; "> Schedule for Practical Examination :</h5>
								</div>	
							</div>
							<div class="row">
							<div class="col-sm-12" >
							<?php

if ((@$student['exam_subjects']['P'])){
    echo "<table class='col-sm-12;'><tr>";
    $n = 1;
	
    foreach ($student['exam_subjects']['P'] as $key => $subject)
    {
		 
        $suppFlag = false;

		$result = CustomHelper::getStudentResult($student['enrollment'], $subject['id']);
		
		if (in_array($subject['id'], $practicalsubjects12) && $student->type == 'Supplementary' && $result == 777){
			$suppFlag = true;
		} 
		
        if ($n % 2 > 0)
        {
            echo "</tr>";

            echo "<tr>";
        }
		?>
        @if($suppFlag === false)
 	 								
			<td class="col-sm-4" style='font-size:16px;'>
				@if(@$subject['exam_date_start'])
					@php echo date('d-m-Y', strtotime($subject['exam_date_start'])); ?> TO <?php echo date('d-m-Y', strtotime($subject['exam_date_end'])); @endphp : 
					<?php echo ucwords($subject['name']); ?> 
				@endif 
			</td> 					
								 
        @endif
      <?php   $n++;
    }
    echo "</tr>";

    echo "</table>";
} ?>
							</div>							
							</div>
							<div class="row" style="border-top:1px dotted #CCCCCC;border-bottom:1px dotted #CCCCCC;margin:5px 5px 0px 5px;padding:5px 0px 0px 0px;">
						<table class="table2" id="instructions" role="grid" aria-describedby="example2_info" style="width:100%;padding:25px;">
											<tbody>
												<!--<tr>
													<td class="hallticketbackcss">
													<div>RAJASTHAN STATE OPEN SCHOOL</div>
													<div>Dr. R.K. Shiksha Sankul</div>
													<div>J.L.N. Marg, Jaipur-302017</div>
													<div>MARCH-MAY 2019 Examination (Hall Ticket)</div>
													</td>													
												</tr>-->
												<tr><td colspan=4 class="hallticketbackcss"><div style="text-align:left;">परीक्षार्थियों के लिए निर्देशः</div></td></tr>
												<tr>
													<td colspan=4>
														<?php ?> 
														<ol style="padding-left:1px;margin-bottom: 0px;border:0px">
															<li>सैद्धांतिक परीक्षा के समय का परीक्षार्थी स्वयं ध्यान रखें। प्रायोगिक परीक्षाएं निर्धारित परीक्षा केन्द्रों पर ही आयोजित होगी। इस हेतु परीक्षा केन्द्र पर सम्पर्क कर परीक्षा समय की जानकारी प्राप्त करें।</li>
															<li>परीक्षार्थी को परीक्षा कक्ष में प्रवेश के लिए प्रवेश पत्र तथा फोटो युक्त आईडी/पहचान पत्र प्रस्तुत करना आवश्यक है।</li>
															<li>परीक्षा से पूर्व प्रवेश पत्र पर अंकित फोटो एवं समस्त प्रविष्टियों का प्रमाणीकरण संदर्भ केन्द्र प्रभारी से आवश्यक रूप से करवा लेवें।</li>
															<li>परीक्षार्थी परीक्षा दिनांक एवं विषय कोड का पूरा ध्यान रखें।</li>
															<li>दृष्टिहीन,मानसिक,विमंदित,लिखने में अक्षम परीक्षार्थी श्रुत लेख की सुविधा के लिए चिकित्सकों  द्वारा प्रदत्त प्रमाण पत्र के साथ परीक्षा से सात दिन पूर्व केन्द्राधीक्षक से सम्पर्क करे ।</li>
															<li>परीक्षा भवन में आर.एस.ओ.एस. द्वारा नियुक्त केन्द्राधीक्षक/वीक्षक/पर्यवीक्षक/उड़नदस्तों के सदस्य आदि को परीक्षार्थी की तलाशी तथा आपत्तिजनक सामग्री प्राप्त करने का अधिकार होगा। इसमें किसी भी प्रकार की आपत्ति/मनाही/अन्य उत्पात दुराचार की श्रेणी में माना जावेगा।</li>
															<li>प्रश्न पत्र पर निर्धारित स्थान पर परीक्षार्थी को अपना रोल नण् एवं हस्ताक्षर प्रत्रक में उत्तरपुस्तिका क्रमांक लिखना अनिवार्य है।</li>
															<li>प्रश्न पत्र हल करने के पश्चात् उत्तर पुस्तिका व अन्त में समाप्त लिख कर रिक्त पृष्ठों को तिरछी लाइन से काटें।</li>
														</ol> 								
													</td>  
												</tr>
												
												
										
											</tbody>
										</table>
					</div>
				</div>
							<div class="row">
								<div class="col-xs-11">
									<div class="col-xs-6" >
										<p style="text-align:center;height:42px;margin-bottom:0px">
											
									<img src="{{asset('public/app-assets/images/favicon/signatureinpdf2.jpg')}}" style="width: 50px; height: 40px;" alt="image upload button"/>	

											</p>
										<p style="font-weight:bold;text-align:center;margin-bottom:0px">Asstt.Director Exam.</p>									
									</div>	
									<div class="col-xs-6" style="height:5%;">
									<p style="text-align:right;margin-bottom:6px;margin-top:10px;"><?php ?></p>
									<img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" style="width: 50px; height: 40px;" alt="image upload button"/>
										<p style="text-align:right;font-weight:bold;margin-bottom:0px">Secretary</p>		
									</div>
								</div>
								<div class="col-xs-1"><?php echo $hallticketcounter; ?></div>
							</div>
						</div>
					</div>
					<?php /*if($counter %2!=0){ ?>
						<div class="dottedborderstyle"></div>
					<?php }*/ ?>
				</div>
			
			<?php
$hallticketcounter++;
$counter++;

if ($counter == 3)
{
    $counter = 1;
?>
			<div style = "display:block; clear:both; page-break-after:always;"></div>
			<?php
}

} ?>		
		 <div style = "display:block; clear:both; page-break-after:always;"></div>
		 <?php
}
?>
	<?php
}
else
{
    echo 'No Record Found!';
} ?>
</section>





<style>
.dottedborderstyle{
	border-top: 4px solid black !important;
	margin-top:5px !important;
	margin-bottom:5px !important;
	margin-left:2px !important;
	margin-right:2px !important;
}
#instructions ol li{margin-top:4px;font-size:15px;}
.admitCard2{
	font-family: Helvetica, Sans-Serif;
}
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
	font-size:16px;
	font-weight:normal;
}
.page-headerh4{
	margin:20px 0px 0px 0px;
	padding:0px 0px 0px 0px;
	font-size:16px;
	font-weight:normal;
	
}
h2{font-size:18px;margin:10px 0px 5px 0px;}
td h4{font-size:14px;margin:10px 0px 5px 0px;}
</style>
