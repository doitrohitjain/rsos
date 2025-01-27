<script src="{!! asset('public/app-assets/js/jquery-3.5.1.min.js') !!}"></script>
@php 
	use App\Component\ResultProcessCustomComponent; 
	use App\Component\ThoeryCustomComponent;
	$result_Process_component_obj = new ResultProcessCustomComponent;

 @endphp
<style>
	.main-header .navbar-custom-menu, .main-header .navbar-right { 
		display: none;
	}
	@media print {
		.headsection {
			display: none;
		}
	} 
	.font{
		font-family: Arial, sans-serif;
	}
	button, .button {
		background-color: #4CAF50;
		border: none;
		color: white;
		padding: 15px 32px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 16px;
		margin: 4px 2px;
		cursor: pointer;
		font-family: Arial, sans-serif;
	}
	@page {
  size: auto;
  margin: 3mm;
       }
	 
</style>

<div class="tab-content  bg-gray headsection">  
</div>
	<div class="box box-warning2 box-solid " id="printarea"> 
		<div id="hallticketdownloadform" class="row pad "><?php 
					if(isset($students) && !empty($students)){
						?>
						<div class="row pad">
							<div class="col-sm-12 font" style="font-size:18px;">
							<div class="col-sm-12 text-center font">
								<center>
									<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px; height:70px;" alt="image upload button">
									<h3 class="font" style="padding: 3; margin:3;">Rajasthan State Open School</h3>
									<span class="font">@if($students->course == 10)
										<b>Secondary Result</b>
										@elseif($students->course == 12)
										<b>Senior Secondary Result</b>
										@endif
										<b>{{@$exam_month[@$students->result_sexam_month]}} {{ @$getresultrsosyesrs[@$students->exam_year] }}</b><h4 class="font" style="padding: 3; margin:3;">Provisional Marksheet</h4></span>
										<?php if(@$students->revised && @$students->revised == 1){?>
									<div style="text-align:center;"><h3 style="color:green;">(Revised)</h3></div>
									<?php } else if(@$students->revised && @$students->revised == 2){?>
									<div  style="text-align:center;"><h3 style="color:red;">No Change</h3></div>
									<?php }else{?>
									<!-- <div  style="text-align:center;"><h3 style="color:red;">No Change</h3></div> -->
									<?php }?>
									
									
								</center>
							</div>
							<!-- <div class="col-sm-2">
								<br>
								<span><b>क्रमांक: ___________________</b></span>
								<br>
								<span><b>दिनांक: ___________________</b></span>
							</div> -->

							</div>&nbsp;
						    <br><br>
							<div class="col-sm-12 font" style="font-size:18px;">
								<div class="col-sm-12 font" style="margin-bottom: 0.1em;">
									Enrollment : <b>
									<?php 
										if(isset($students->enrollment)){
											echo ($students->enrollment);
										}
									?></b>
								</div>
								<div class="col-sm-12 font" style="margin-bottom: 0.1em;">
									Name of Candidate : <b>
									<?php 
										if(isset($students->name)){
											echo ($students->name);
										}
									?></b>
								</div>
								<div class="col-sm-12 font" style="margin-bottom: 0.1em;">
									Father's Name : <b>
									<?php 
										if(isset($students->father_name)){
											echo ($students->father_name);
										}
									?></b>
								</div>
								<div class="col-sm-12 font" style="margin-bottom: 0.1em;">
									Mother's Name : <b>
									<?php 
										if(isset($students->mother_name)){
											echo ($students->mother_name);
										}
									?></b>
								</div> 

								<div class="col-sm-12 font" style="margin-bottom: 0.1em;">
									Date of Birth : <b>
									<?php  
										if(isset($students->dob)){
											echo date("d/m/Y",strtotime($students->dob));
										}
									?></b>
								</div> 
								<div class="col-sm-12 font">
									Class : <b>
									<?php 
										if(isset($students->course)){
											echo $course[$students->course] . "<sup></sup>";
										}
									?></b>
								</div>
								
								
							</div> 
							
						</div>&nbsp;
						<Br><Br>
						<?php 
						if(isset($students) && !empty($students)){
						if($students->final_result !== "RWH"){	 ?>
						<div>
							<table class="table font 
							" style="border:1px solid; width:100%;  border-collapse: collapse;">
								<tr>
									<th style="border:1px solid;height:12px;width:5%;">Sr.No</th>
									<th style="border:1px solid;height:12px;width:40%;text-align: left;">&nbsp;Subject Name (Code)</th>
									<th style="border:1px solid;height:12px;width:10%;">Max Marks</th>
									<th style="border:1px solid;height:12px;width:10%;">Marks Theory</th>
									<th style="border:1px solid;height:12px;width:10%;">Marks Practical</th>
									<th style="border:1px solid;height:12px;width:9%;">Marks Sessional</th>
									<th style="border:1px solid;height:12px;width:8%;">Total Marks</th>
									<th style="border:1px solid;height:12px;width:8%;">Result</th>
								</tr>
								<?php $counter=1;
								@$additional=(isset($students->additional_subjects)&&!empty($students->additional_subjects))?unserialize($students->additional_subjects):"";
								@$subjectArr=$studentexamsubjects;
								@$additionalSubject=array(); 
								@$newSubjectArr=array();
								foreach($subjectArr  as $key => $subject ){
									if(!empty($additional) && isset($additional[$subject->subject_id])){
										@$additionalSubject[]=$subjectArr[$key];
										unset($subjectArr[$key]);
									}
								}    
								@$newsubARR=array_merge($subjectArr,$additionalSubject);

								foreach($newsubARR as $k => $v){
								@$gtMax = $result_Process_component_obj->getSubjectMaxMinMarksMaster($v->subject_id,81);  ?>
								<tr>
									<td  style="border:1px solid; padding:6px;text-align: center;">&nbsp;<?php  echo $counter++;?></td>
									<td  style="border:1px solid;height:12px;">&nbsp;<?php echo $subject_list[$v->subject_id];?>
									<?php
									if(!empty($additional) && isset($additional[$v->subject_id])){
										echo "<strong>(Additional)</strong>";
									}
									?>
									</td>
									<td  style="border:1px solid;height:12px;text-align: center;">&nbsp;<?php echo $gtMax['GT_MAX'];?></td>
									<td  style="border:1px solid;height:12px;text-align: center;">
									<?php 
										if($v->final_theory_marks!=999){
											if($v->final_theory_marks=="222"){
												echo "&nbsp;RW";
											}else{
												echo "&nbsp;".$v->final_theory_marks;
											}
										}else{
											echo "&nbsp;AB";
										}
									
									?></td>
									<td  style="border:1px solid;height:12px;text-align: center;"><?php 
										if($v->final_practical_marks!=999){
											if($v->final_practical_marks=="222"){
												echo "&nbsp;RW";
											}else{
												if(in_array($v->subject_id,array(3,4,7,13,16,23,24,25,28,33,35,37,38,39))){ echo "&nbsp;".$v->final_practical_marks;
												}else{
													echo "&nbsp;";
													echo "-";
												}
											}
										} 
										else if(!in_array($v->subject_id,array(3,4,7,13,16,23,24,25,28,33,35,37,38,39))){
											echo "&nbsp;-";
										} else{
											echo "&nbsp;AB";
										}
									
									?></td>
									<td  style="border:1px solid;height:12px;text-align: center;">
									<?php 
									    if(@$students->stream == 1){
										if(@$v->sessional_marks_reil_result!=999){
											if(@$v->sessional_marks_reil_result=="222"){
												echo "&nbsp;RW";
											}else{
												echo "&nbsp;";
											echo ($v->sessional_marks_reil_result>=0)?$v->sessional_marks_reil_result:"-";
											}
										}else{
											echo "&nbsp;-";
										}
										}else{
											echo "&nbsp;-";
										}
									
									?>
									</td>
									
									<td  style="border:1px solid;height:12px;text-align: center;">
									<?php
										if(@$v->total_marks=="222"){
											echo "&nbsp;RW";
										}elseif(@$v->total_marks=="444"){
											echo "&nbsp;RWH";
										}else{
											echo "&nbsp;".$v->total_marks;
										}
									?> </td>
									
									<td  style="border:1px solid;height:12px;text-align: center;">&nbsp;
									@if(@$v->final_result == 'P' || @$v->final_result == 'p')
											<?php echo @$v->final_result ?>
                  						@elseif($result_type[@$v->final_result])
											<?php echo @$result_type[@$v->final_result] ?>
									@elseif(@$v->final_result == "")
									@endif

								</td>
								</tr>
								<?php } ?>
							</table>
						</div>
						
						<?php } // end RWH conditions
						}
					?>
					
					
					
					<?php if(isset($students) && !empty($students)){ 
						if($students->final_result!=="RWH"){	
						?></br>
						<div class="col-sm-12">
							<table class="table font" style="border:1px solid; text-align: center;width:100% ">
								<tr>
								<td>&nbsp;TOTAL</td>
									<td ><b><?php echo @$students->total_marks ?></b></td>
									<td>RESULT</td>
									<td><b><?php echo @$students->final_result?></b></td>
									<td colspan="2" ></td>
								</tr>
								<!-- <tr> 
									<th>RESULT</th>
									<td><b><</b></td>
								</tr> -->
								
							</table>
						</div>
						
						<?php }else{?>
							<div class="col-sm-12">
							<table class="table font" style="border:1px solid; font-size:14px;width:100% ">
								<tr>
									<th>RESULT</th>
									<th style="font-size:25px;color:red;"><b><?php echo @$students->final_result?></b></th>
									<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
									</tr>
							</table>
						</div>
						
						<?php }
						}
					?>
					
					
					<div class="row pad">
						<div class="col-sm-offset-0 col-sm-12 font" style="font-size:16px;">
							<p><b>P – Passed <br>
							SYC – Subject Yet to be Cleared
							<br>SYCT – Subject Yet to be Cleared in Theory
							<br> SYCP – Subject Yet to be Cleared in Practical
							<br><b>RWH – Result Withheld</b>
							<br><b>XXXX – Yet to be Cleared</b>
							</p>
							<p>
			
					</p>
							<?php  //if(!empty($isOldDisclaimer)&&$isOldDisclaimer=="yes"){ ?>
							<!--<p><b>पुनर्गणना एवं उत्तरपुस्तिका की फोटो प्रति प्राप्त करने के लिए निर्धारित शुल्क सहित आवेदन परिणाम घोषित तिथि के बाद 15 दिवस में RSOS कार्यालय शिक्षा संकुल, जयपुर में पहुंच जाना चाहिये। बाद मियाद प्राप्त आवेदन निरस्त कर शुल्क जब्त कर लिया जायेगा।</b>
							</p>
							<p><b>Disclaimer: The information published on the NET is provisional and subject to confirmation by Rajasthan State Open School (RSOS). RSOS is not responsible for any inadvertent error that may have crept in the Result being published on NET. The details published on NET are for immediate information to the Learners.</b>-->
							</p>
							<?php /*}else{?>
							<p><b>यह अग्रिम अंकतालिका अभ्यर्थी के प्रार्थना पत्र पर तथा इस हेतु गठित समिति कि अभिशंषा के आधार पर जारी की जा रही है</b> </p>
							<p><b>This advance score is being issued on the application of the candidate and on the basis of the committee formed for this.</b>
							</p>
							<?php }*/ ?>
							
						</div>
					</div>
					<?php /* if(!empty($isOldDisclaimer)&&$isOldDisclaimer!="yes"){ ?>
						<table class="table">
							<tr>
								<td width="60%"></td>
								<td width="40%">
								<p> 
									<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;सचिव</b>
									<br>
									राजस्थान स्टेट ओपन स्कूल, जयपुर
								
							</p>
							</td></tr>
						</table>
						
					
					<?php } */ ?>
				<?php
					}
				?>
				
		</div> 
	</div> 
		
	