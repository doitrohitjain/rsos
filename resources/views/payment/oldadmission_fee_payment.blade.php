
<style>
	.main-header .navbar-custom-menu, .main-header .navbar-right { 
		display: none;
	}
	@media print {
		.headsection {
		display: none;
		}
	}
	@page { size: auto;  margin: 0mm; } 
	.captcha-div .error-message { 
		color : #e44b72 !important;
	}
	.starCss{
		color:red !important;
		font-size: 16px;
	}
</style>

<div class="box box-default box-solid headsection">
	<div class="tab-content  bg-gray">  
		<div class="row pad ">
			<div class="col-md-12"> 
				<div class="col-md-7">  
					<h3 style=""> 
						<!-- <img src="/rsos/img/new.jpg" height="20" alt=""> -->
						Student Admission Fee Payment
					</h3> 
				</div> 
							
				<?php if(isset($student) && !empty($student)){ ?>
				<div class="col-sm-1 text-right">
					<a class="btn  btn-info" href="javascript::void(0)" onclick="window.print()">Print Result</a>
				</div> 
				<?php } ?>
							
			</div>	
			<div class="col-md-12" style="color:red;font-size:20px;"> 
				<marquee>
					<p>
						<img src="/rsos/img/new.jpg" height="10" alt="">
						प्रवेश शुल्क का भुगतान करने के लिए कृपया विवरण भरें।(Please filled details to pay admission fees.)
					</p>
				</marquee> 
			</div> 			
		</div>
	</div>
</div>
<div class="box box-warning2 box-solid ">


<?php
// `is_show_disclaimer` Flag set in setting.php at root 
 
// pr($students);
//echo $this->element('admission_menu',array('active'=>''));


?>

<style>
	.hide {
		display: none;
	}


#callforhelp {
    float: right;
    border-radius: 10px;
    background: #fff;
    color: #000;
    font-size: 20px;
    width: 260px;
    margin-left: 50px;
    padding: 0 10px;
    display: none;
}
#callforhelp2 {
    float: right;
    border-radius: 10px;
    background: #fff;
    color: #000;
    font-size: 14px;
    width: 260px;
    margin-left: 50px;
    padding: 0 10px;
}
</style>

 <div id="hallticketdownloadform" class="row pad ">
<?php //echo $searchBoxClass; ?>
<div class="col-md-12 <?php echo $searchBoxClass; ?>">




	<?php echo $this->Form->create($model, array('id'=>'filterForm'));?>
		 <div class="row no-print">
		 	<div class="col-sm-12 col-sm-offset-3">
				<div class="col-md-3" >
					<?php $fld = 'enrollment'; $lbl = __('Enrollment No. <span class="starCss">*</span>'); ?>
					<div class="form-group <?php echo ($this->Form->error($model . "." . $fld)) ? "has-error" : "";?>">
					<?php echo $this->Form->label($model . "." . $fld, $lbl . ":" );?>
				<?php  echo $this->Form->text($model . "." . $fld,array('class'=>'requiredItem form-control','empty'=>'Select ' . $lbl,'required'=>'false')); 
					echo $lbl = __('<span style="color:blue;font-size:15px;">ex. XXXXXXXXXX</span>');
					if ($this->Form->isFieldError($model . "." . $fld)) {
						echo '<span class="help-block">' . $this->Form->error($model . "." . $fld) . '</span>';
					}  
					else { 
						echo '<span class="">&nbsp;</span>';
					}
				?> 
					<!-- <p><span class="bg-maroon pad">eg: 
					XXXXXXXX</span></p>	 -->
					</div>
				</div>	
				<div class="col-sm-1">
					<p style="text-align:center;font-size:24px;"><br></p>
				</div>
				<div class="col-md-3">
				<?php $fld = 'dob'; $lbl = __('Date of Birth(dd/mm/yyyy) <span class="starCss">*</span>'); ?>
				<div class="form-group <?php echo ($this->Form->error($model . "." . $fld)) ? "has-error" : "";?>">
					<?php echo $this->Form->label($model . "." . $fld, $lbl . ":" );?>
					<?php  echo $this->Form->text($model . "." . $fld,array('class'=>'requiredItem  form-control','empty'=>'Select ' . $lbl,'required'=>'false'));
					echo $lbl = __('<span style="color:blue;font-size:15px;">ex. For dob 25th November, 1990 use (25/10/1990)</span>');
					
					if ($this->Form->isFieldError($model . "." . $fld)) {
						echo '<span class="help-block">' . $this->Form->error($model . "." . $fld) . '</span>';
					}  
					else { 
						echo '<span class="help-block">&nbsp;</span>';
					}
					?>
					
					</div>
				</div>	
			</div>
			<div class="col-sm-12 col-sm-offset-3">
				<div class="col-sm-3">
				<?php $fld = 'aadhar_number'; $lbl = __('Aadhar Number <span class="starCss">*</span>'); ?>
				<div class="form-group <?php echo ($this->Form->error($model . "." . $fld)) ? "has-error" : "";?>">
					<?php echo $this->Form->label($model . "." . $fld, $lbl . ":" );?>
					<?php  echo $this->Form->text($model . "." . $fld,array('class'=>'form-control','empty'=>'Select ' . $lbl,'required'=>'false'));
					echo $lbl = __('<span style="color:blue;font-size:15px;">ex. XXXXXXXXXX</span>');
					
					if ($this->Form->isFieldError($model . "." . $fld)) {
						echo '<span class="help-block">' . $this->Form->error($model . "." . $fld) . '</span>';
					} else { 
						echo '<span class="help-block">&nbsp;</span>';
					}
					?> 
					</div>
				</div>		
				<div class="col-sm-1">
					<p style="text-align:center;font-size:24px;"><br>OR</p>
				</div>
				<div class="col-sm-3">
					<?php $fld = 'jan_aadhar_number'; $lbl = __('Jan Aadhar Number <span class="starCss">*</span>'); ?>
				<div class="form-group <?php echo ($this->Form->error($model . "." . $fld)) ? "has-error" : "";?>">
					<?php echo $this->Form->label($model . "." . $fld, $lbl . ":" );?>
					<?php  echo $this->Form->text($model . "." . $fld,array('class'=>'form-control','empty'=>'Select ' . $lbl,'required'=>'false'));
					echo $lbl = __('<span style="color:blue;font-size:15px;">ex. XXXXXXXXXX</span>');
					if ($this->Form->isFieldError($model . "." . $fld)) {
						echo '<span class="help-block">' . $this->Form->error($model . "." . $fld) . '</span>';
					}  
					else { 
						echo '<span class="help-block">&nbsp;</span>';
					}
					?> 
					</div>
				</div>		
			</div>
	
			<div class="col-sm-12 col-sm-offset-3">
				<div class="col-md-4">
				<?php $fld = 'captcha'; $lbl = __('Captcha <span class="starCss">*</span>'); ?>
				<div class="form-group <?php echo ($this->Form->error($model . "." . $fld)) ? "has-error" : "";?> captcha-div">
					<?php echo $this->Form->label($model . "." . $fld, $lbl . ":" );?>
					<?php 
						$op['width']=160;
						$op['height']=32;
						$op['model']= $model;
						$op['type']= 'math';
						
						$this->Captcha->render($op);
						?>
					<?php // echo $this->Form->text($model . "." . $fld,array('class'=>'form-control input-sm','empty'=>'Select ' . $lbl)); ?>
					
					</div>
				</div>	

				<div class="col-sm-2" style="padding-top:25px;">
					<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> SEARCH</button>
					<a href="<?php echo $this->Html->url(array('action'=>'admission_fee_payment'));?>" class="btn  btn-default"><i class="fa fa-refresh"></i> Reset</a>
				</div>
			</div> 
			
			<?php echo $this->Form->end();
	 
	 ?>
</div>


			<?php 
				if(isset($students) && !empty($students)){
					?>

					<div class="row pad">
						<div class="col-sm-12 " style="font-size:14px;">
						<div class="col-sm-12 text-center">
							<center>
								<?php echo $this->Html->image('rsoslogo.jpg',array("style" => "width:50px;height:50px;"));?>
								<h3>Rajasthan State Open School</h3>
								<h4>Senior Secondary 
								Result <?php 
								if(isset($students['ExamNewResult']['exam_month'])){
									echo $students['ExamNewResult']['exam_month'] . ',';
									
								}
								?>
								2021
								<?php if(isset($students['ExamNewResult']['reval_result_changed']) && $students['ExamNewResult']['reval_result_changed'] == 2){?>
							<div style="text-align:center;"><h3 style="color:green;">(Revised)</h3></div>
								<?php } else{?>
							<div  style="text-align:center;"><h3 style="color:red;">No Change</h3></div>
							<?php }?>
								<!-- Class- -->
								<?php //echo $students['Application']['course'] . "<sup></sup>";?></h4>
								<?php /* if(!empty($isOldDisclaimer)&&$isOldDisclaimer!="yes"){ ?>
								<h4><b>Provisional Marksheet</b> </h4>
								<?php } */?>
								
							</center>
						</div>
						<!-- <div class="col-sm-2">
							<br>
							<span><b>क्रमांक: ___________________</b></span>
							<br>
							<span><b>दिनांक: ___________________</b></span>
						</div> -->

						</div>
					
						<div class="col-sm-12 " style="font-size:14px;">
							<div class="col-sm-12">
								Enrollment : <b>
								<?php 
									if(isset($students[$modelName][0][$modelName]['enrollment'])){
										echo h($students[$modelName][0][$modelName]['enrollment']);
									}
								?></b>
							</div>
							<div class="col-sm-12">
								Name of Candidate : <b>
								<?php 
									if(isset($students['Student']['name'])){
										echo h($students['Student']['name']);
									}
								?></b>
							</div>
							<div class="col-sm-12">
								Father's Name : <b>
								<?php 
									if(isset($students['Student']['father_name'])){
										echo h($students['Student']['father_name']);
									}
								?></b>
							</div>
							<div class="col-sm-12">
								Mother's Name : <b>
								<?php 
									if(isset($students['Student']['mother_name'])){
										echo h($students['Student']['mother_name']);
									}
								?></b>
							</div> 

							<div class="col-sm-12">
								Date of Birth : <b>
								<?php  
									if(isset($students['Application']['dob'])){
										echo $students['Application']['dob'];
									}
								?></b>
							</div> 
							<div class="col-sm-12">
								Class : <b>
								<?php 
									if(isset($students['Application']['course'])){
										echo $students['Application']['course'] . "<sup></sup>";
									}
								?></b>
							</div>
							 
							 
						</div> 
						 
					</div>
					<?php 
					if(isset($students[$modelName][0]) && !empty($students[$modelName][0])){
					if($students["ExamNewResult"]['final_result']!=="RWH"){
							
					?>
					<div class="col-sm-offset-0 col-sm-11">
						<table class="table" style="border:2px solid;">
							<tr>
								<th style="border:2px solid;height:10px;">Sr. No.</th>
								<th style="border:2px solid;height:10px;">Subject Name (Code)</th>
								<th style="border:2px solid;height:10px;">Max Marks</th>
								<th style="border:2px solid;height:10px;">Marks Theory</th>
								<th style="border:2px solid;height:10px;">Marks Practical</th>
								<th style="border:2px solid;height:10px;">Marks Sessional</th>
								<th style="border:2px solid;height:10px;">Total Marks</th>
								<th style="border:2px solid;height:10px;">Result</th>
							</tr>
							<?php $counter=1;
							$additional=(isset($students['ExamNewResult']['additional_subjects'])&&!empty($students['ExamNewResult']['additional_subjects']))?unserialize($students['ExamNewResult']['additional_subjects']):"";
							$subjectArr=$students[$modelName];
							$additionalSubject=array(); 
							$newSubjectArr=array();
							foreach($subjectArr  as $key => $subject ){
								if(!empty($additional) && isset($additional[$subject[$modelName]['subject_id']])){
									$additionalSubject[]=$subjectArr[$key];
									unset($subjectArr[$key]);
								}
							}    
							$newsubARR=array_merge($subjectArr,$additionalSubject);

							foreach($newsubARR as $k => $v){
							$gtMax=$this->Custom->getSubjectMaxMinMarksMaster($v[$modelName]['subject_id'],81);  ?>
							<tr>
								<td  style="border:2px solid;height:10px;"><?php  echo $counter++;?></td>
								<td  style="border:2px solid;height:10px;"><?php echo $subjectList[$v[$modelName]['subject_code']];?>
								<?php
								if(!empty($additional) && isset($additional[$v[$modelName]['subject_id']])){
									echo "(Additional)";
								}
								?>
								</td>
								<td  style="border:2px solid;height:10px;"><?php echo $gtMax['GT_MAX'];?></td>
								<td  style="border:2px solid;height:10px;">
								<?php 
									if($v[$modelName]['theory_marks']!=999){
										if($v[$modelName]['theory_marks']=="222"){
											echo "RW";
										}else{
											echo $v[$modelName]['theory_marks'];
										}
									}else{
										echo "AB";
									}
								
								?></td>
								<td  style="border:2px solid;height:10px;"><?php 
									if($v[$modelName]['practical_marks']!=999){
										if($v[$modelName]['practical_marks']=="222"){
											echo "RW";
										}else{
											echo ($v[$modelName]['practical_marks']>0)?$v[$modelName]['practical_marks']:"-";
										}
									} else  if(in_array($v[$modelName]['subject_id'],array('18','19','20','21','22','26',27,'29','31','32','34','36'))){
										echo "-";
									} else{
										echo "AB";
									}
								
								?></td>
								<td  style="border:2px solid;height:10px;">
								<?php 
									if($v[$modelName]['sessional_marks_reil_result']!=999){
										if($v[$modelName]['sessional_marks_reil_result']=="222"){
											echo "RW";
										}else{
											echo ($v[$modelName]['sessional_marks_reil_result']>0)?$v[$modelName]['sessional_marks_reil_result']:"-";
										}
									}else{
										echo "-";
									}
								
								?>
								</td>
								
								<td  style="border:2px solid;height:10px;">
								<?php
									if($v[$modelName]['total_marks']=="222"){
										echo "RW";
									}elseif($v[$modelName]['total_marks']=="444"){
										echo "RWH";
									}else{
										if($students["ExamNewResult"]['final_result']!=="RWH"){
											echo $v[$modelName]['total_marks'];
										}else{
											echo "-";
										}
									}
								?> </td>
								
								<td  style="border:2px solid;height:10px;"><?php echo $v[$modelName]['final_result']?></td>
							</tr>
							<?php } ?>
						</table>
					</div>
					
					<?php } // end RWH conditions
					}
				?>
				
				
				
				<?php if(isset($students["ExamNewResult"]) && !empty($students["ExamNewResult"])){ 
					//if(in_array($students["ExamNewResult"]['reval_result_changed'],array(1,2))){

					?>
					<div class="col-sm-offset-0 col-sm-11">
						<table class="table" style="border:2px solid;margin-top:5px;">
							<tr>
								<?php 						if($students["ExamNewResult"]['final_result']!=="RWH"){	 ?>
								<td style="border:2px solid black;border-right:none !important;">TOTAL</td>
								<td style="border:2px solid black;border-left:none !important;border-right:none !important;"><b><?php echo $students["ExamNewResult"]['total_marks']?></b></td>
								<?php } ?>
								<td style="border:2px solid black;border-right:none !important;border-left:none !important;">RESULT</td>
								<td style="border:2px solid black;border-right:none !important;border-left:none !important;"><b><?php echo $students["ExamNewResult"]['final_result']?></b></td>
								<td colspan="2" style="border:2px solid black;border-left:none !important;" ></td>
							</tr>
						
							 
						</table>
					</div>
					
					<?php  //}else{?>
						<!--<div class="col-sm-offset-0 col-sm-11">
						<table class="table" style="border:2px solid black; font-sze:16px; ">
							<tr>
								
								<td><b>As you have applied for Revaluation there is "No CHANGE" in your result
								</b></td>
							</tr>
						</table>
					</div>-->
					
					<?php //}
					}
				?>			
				<div class="row pad">
					<div class="col-sm-offset-0 col-sm-12" style="font-size:12px;">
						<p><b>P – Passed <br>
						SYC – Subject Yet to be Cleared
						<br>SYCT – Subject Yet to be Cleared in Theory
						<br> SYCP – Subject Yet to be Cleared in Practical
						<br><b>RWH – Result Withheld</b>
						<br><b>XXXX – Fail</b>
						</p>			
						
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
<!--<div>
<p style="font-size:28px;margin-left:20px;"><img src="/rsos/img/new.jpg" height="20" alt=""><a style="color:blue !important;" href="<?php echo $this->webroot.'files/Retotalling12thclass.pdf';?>" target="_blank">NOTE:Click here for Senior Secondary Mar-May 2021 Retotalling Result</a></p>
</div>-->		
		<?php 
echo $this->Html->script(array('plugins/jQuery/jquery-2.2.3.min.js'));


	echo $this->fetch('script');
	?>  
<?php
//echo $this->Html->script(array('plugins/input-mask/jquery.inputmask.js', 'plugins/input-mask/jquery.inputmask.date.extensions.js', 'plugins/input-mask/jquery.inputmask.extensions.js'), array('inline' => false));
// jQuery UI library -->

echo $this->Html->script(array('https://code.jquery.com/ui/1.12.1/jquery-ui.js'), array('inline' => true));
echo $this->Html->css('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array('inline' => true));
?> 	


<?php 
 
	echo $this->Html->script(array('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js'), array('inline' => true));
	echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css', array('inline' => true));
 
?>

	
<?php echo $this->Html->scriptStart(array('inline' => true));?>
$(function() {
	$("#filterForm").on('submit',function(){ 
		var error = 0;
		$( ".requiredItem" ).each(function() {
			if($(this).val() == ""){
				error = 1;
			}
		}); 
		if(error == 1 ){
			$.alert(" <span style='color:red;font-size:24px;'>कृपया * अनिवार्य फ़ील्ड भरें(Please fill * mandatory fields.)</span> ");
			return false;
		}else{
			if($("#AdmissionAadharNumber").val() == "" && $("#AdmissionJanAadharNumber").val() == ""){
				$.alert(" <span style='color:red;font-size:24px;'>कृपया * अनिवार्य फ़ील्ड भरें आधार नंबर या जन आधार नंबर। (Please fill * mandatory filled. Aadhar Number Or Jan Aadhar Nubmber.)</span> ");
				return false;
			}
		}

		if(($("#AdmissionAadharNumber").val().length >= 12 && $("#AdmissionAadharNumber").val().length <= 16) || ($("#AdmissionJanAadharNumber").val().length >= 10 && $("#AdmissionJanAadharNumber").val().length == 10)){
			
		}else{
			$.alert("<span style='color:red;font-size:20px;'>कृपया आधार संख्या या जन आधार संख्या के मान्य अंक भरें।( Please fill in the valid digits of aadhar number or jan aadhar nubmer.)</span> ");
			return false;
		}
		
	}); 

$( "#AdmissionDob" ).datepicker({
			changeMonth: true,
			dateFormat: 'dd/mm/yy',
			changeYear: true,
			yearRange: "c-40:c+40",
			maxDate: new Date(2021,12,28)
		});	 
		
$('.creload').on('click', function() { 
    var mySrc = $("#img<?php echo $model;?>captcha"). attr('src');
    var glue = '?';
    if(mySrc.indexOf('?')!=-1)  {
        glue = '&';
    }
    $("#img<?php echo $model;?>captcha").attr('src', mySrc + glue + new Date().getTime());
    return false;
});

$( "#hallticketlink" ).click(function() {  
 
   $('#hallticketdownloadform').removeClass("hide").addClass("show").show();
});
});

<?php echo $this->Html->scriptEnd();?> 