
<?php 
$this->start('content-header1');?>
<section class="content-header">
<h1> &nbsp;</h1>
<?php if($breadcrumb){?>
<ol class="breadcrumb"><?php foreach($breadcrumb['pages'] as $t => $l) echo '<li>' . $this->Html->link($t,$l,array('escape'=>false)) . '</li>';?>
<li class="active"><?php echo $breadcrumb['active'];?></li></ol>
<?php }?>
</section>

<?php $this->end();?> 

<style>
	.hideShowItem{
		display: none;
	}
</style>

<!-- Default box -->
<?php 
	echo $this->Form->create($model, array("enrollment" => $enrollment));
 	echo $this->Form->hidden($model .'.makepayment', array('value'=>1));
 	echo $this->Form->hidden($model .'.enrollment', array('value'=>$enrollment));
?>	
<div class="box box-warning box-solid" id="cinfo">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo Configure::read('Site.title');?> :: <?php echo Configure::read('Site.admission_academicyear_name');?></h3>

		<div class="box-tools pull-right">

		</div>
		<!-- /.box-tools -->
	</div>
	<!-- /.box-header -->
 
				 
				<div class="box-body">
					<div class="row">
					
					<div class="col-md-12"> 
					<div class="pull-right">  
						<a href="<?php echo $this->Html->url(array('controller'=>'payments','action'=>'admission_fee_payment'));?>" 
								class="btn btn-info"><i class="fa fa-eye"></i> Again Search</a> 
					</div>
					<?php if(isset($student['Student']['challan_tid']) && !empty($student['Student']['challan_tid'])){ ?>
						<div class="callout callout-success" style="margin-bottom: 0!important;"> <h4><i class="fa fa-info"></i> Your Application form fee paid successfully.</h4>
						<?php }else{ ?>
							<div class="callout callout-info" style="margin-bottom: 0!important;"> <h4><i class="fa fa-info"></i> Please pay the Application form fee for proceeding in the application status.</h4>
						<?php } ?>
     				</div>

					<?php 
						if(isset($student['Student']['challan_tid']) && !empty($student['Student']['challan_tid'])){ ?>
						<?php 
							if(isset($Application['Application']['is_lock&submitted']) && $Application['Application']['is_lock&submitted'] == 1 && !empty($Application['Application']['lock&submitted_date'])){
						?>
							<div class="pull-right">
								<img src="/rsos/img/new.jpg" height="40" alt="">
								<a href="<?php echo $this->Html->url(array('controller'=>'Admission','action'=>'downloadpdf',$student['Student']['id']));?>" 
								class="btn btn-success"><i class="fa fa-download"></i> Download Payment PDF</a>
							</div>
						<?php } ?>  
					<?php }   ?> 
 
					</div>
					</div>
					<div class="row pad invoice">
					<div class="col-md-6">
						<?php $fld = 'name'; $lbl = __('Applicant\'s Name'); $lblhi = __('आवेदक का नाम  '); ?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($student['Student'][$fld]) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
							<?php $fld = 'father_name'; $lbl = __('Father\'s Name'); $lblhi = __('पिता का नाम'); ?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($student['Student'][$fld]) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
								<?php $fld = 'mother_name'; $lbl = __('Mother\'s Name'); $lblhi = __('माता का नाम');?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($student['Student'][$fld]) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
							<?php $fld = 'gender_id'; $lbl = __('Gender'); $lblhi = __('लिंग'); ?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h(Configure::read('Site.genders.'.$Application['Application'][$fld])) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
						<?php $fld = 'dob'; $lbl = __('Date of Birth'); $lblhi = __('जन्म तिथि');?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($Application['Application'][$fld]) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
						<?php $fld = 'mobile'; $lbl = __('Mobile'); $lblhi = __('मोबाइल नंबर'); ?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($student['Student'][$fld]) ;  ?></div>
						</div>
					</div>
					<div class="col-md-6">
						   <?php $fld = 'category_a'; $lbl = __('Category A'); $lblhi = __('श्रेणी ए'); ?>
						<div class="row">
							<div class="col-xs-6"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-6"><?php echo h($categorya[$Application['Application'][$fld]]) ;  ?></div>
						</div>
					</div>

					<div class="col-sm-12">
						 

						<?php
							if(!empty($GeneralFee) || !empty($ReadmFee) || !empty($PartFee) || !empty($ItiFee)){
							?>
							<div class="row pad">
							<div class="col-md-12">
							<fieldset class="scheduler-border col-md-12 col-xs-12 left">
							<legend class="scheduler-border fieldsetLable-newll">
							<label class="black">Exam Fees Details (परीक्षा शुल्क का विवरण)</label>
							</legend>	
							<div class="row">
							<div class="col-md-12">
									<div class="col-md-12">
									<div class="box">
										<!-- /.box-header -->
										<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap"><div class="row"><div class="col-sm-6"></div><div class="col-sm-6"></div></div><div class="row"><div class="col-sm-12"><table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
											<thead>
											<tr role="row">
											<?php
											
											//if(!empty($Application['Application']['category_a']) && $Application['Application']['category_a']!=7){
											if(!empty($PartFee)){
											?>
											<th class="sorting">Registration Fees</th>
											<th class="sorting">Exam Fees</th>
											<th class="sorting">Late Fees</th>
											<th class="sorting">Practical Fees</th>
											<th class="sorting">Forwarding Fees</th>
											<th class="sorting">Online Services Fees</th>
											<th class="sorting">Total</th>
										
											</thead>
											<tbody>
											<tr role="row" class="odd">
											<?php
											
											//if(!empty($PartFee)){
											?>
										
												<td>
												<?php echo $PartFee['PartFee']['registration_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['exam_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['late_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['practical_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['forward_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['online_services_fees']; ?>
												</td>
												<td>
												<?php echo $PartFee['PartFee']['total']; ?>
												</td>
											
											<?php //}
											}
											if(!empty($ItiFee)){?>
												<th class="sorting">Registration Fees</th>
												<th class="sorting">Exam Fees</th>
												<th class="sorting">Late Fees</th>					
												<th class="sorting">Forwarding Fees</th>
												<th class="sorting">Online Services Fees</th>
												<th class="sorting">Total</th>
											
												</thead>
												<tbody>
												<tr role="row" class="odd">
												<td>
												<?php echo $ItiFee['ItiFee']['iti_admi_fees']; ?>
												</td>
												<td>
												<?php echo $ItiFee['ItiFee']['exam_fees']; ?>
												</td>
												<td>
												<?php echo $ItiFee['ItiFee']['late_fees']; ?>
												</td>				
												<td>
												<?php echo $ItiFee['ItiFee']['forward_fees']; ?>
												</td>
												<td>
												<?php echo $ItiFee['ItiFee']['online_services_fees']; ?>
												</td>
												<td>
												<?php echo $ItiFee['ItiFee']['total']; ?>
												</td>
											<?php }
											if(!empty($GeneralFee)){?>
											
											<?php if($Application['Application']['gender_id']==2 || $Application['Application']['category_a']==6 || $Application['Application']['category_a']==2 || $Application['Application']['category_a']==3 || $Application['Application']['disability']!=10){ ?>
											<th class="sorting">Registration Fees </th>
											<?php }
											else { ?>
											<th class="sorting">Admission Fees</th>
											<?php }?>
											
											<th class="sorting">Late Fees</th>
											<th class="sorting">Additional Subject Fees</th>
											<th class="sorting">TOC</th>
											<th class="sorting">Practical Fees</th>
											<th class="sorting">Forwarding Fees</th>
											<th class="sorting">Online Services Fees</th>
											<th class="sorting">Total</th>
										
											</thead>
											
											<tbody>
											<tr role="row" class="odd">
											<?php
											
											if(!empty($GeneralFee)){
											?>
											<?php  if($Application['Application']['gender_id']==2 || $Application['Application']['category_a']==6 || $Application['Application']['category_a']==2 || $Application['Application']['category_a']==3 || $Application['Application']['disability']!=10){ ?>
												<td>
												<?php echo $GeneralFee['GeneralFee']['relaxation_fees']; ?>
												</td>
											<?php }
											else { ?>
											<td>
												<?php echo $GeneralFee['GeneralFee']['general_admi_fees']; ?>
												</td>
											<?php }?>
												<td>
												<?php echo $GeneralFee['GeneralFee']['late_fees']; ?>
												</td>
												<td>
												<?php echo $GeneralFee['GeneralFee']['add_sub_fees']; ?>
												</td>
												<td>
												<?php echo $GeneralFee['GeneralFee']['toc_fees']; ?>
												</td>
												
												<td>
												<?php echo $GeneralFee['GeneralFee']['practical_fees']; ?>
												</td>
												<td>
												<?php echo $GeneralFee['GeneralFee']['forward_fees']; ?>
												</td>
												<td>
												<?php echo $GeneralFee['GeneralFee']['online_services_fees']; ?>
												</td>
												<td>
												<?php echo $GeneralFee['GeneralFee']['total']; ?>
												</td>
												
											<?php } }
											/** Readmission fees */
											if(!empty($ReadmFee)){?>
											
											
											<th class="sorting">Admission Fees</th>
											<th class="sorting">Exam Fees</th>
											
											<th class="sorting">Late Fees</th>
											<th class="sorting">Additional Subject Fees</th>
											<th class="sorting">TOC</th>
											<th class="sorting">Practical Fees</th>
											<th class="sorting">Forwarding Fees</th>
											<th class="sorting">Online Services Fees</th>
											<th class="sorting">Total</th>
										
											</thead>
											
											<tbody>
											<tr role="row" class="odd">
											<?php if(!empty($ReadmFee)){?>
											<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['re_admi_fees']; ?>
												</td>	
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['exam_fees']; ?>
												</td>					
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['late_fees']; ?>
												</td>
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['add_sub_fees']; ?>
												</td>
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['toc_fees']; ?>
												</td>					
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['practical_fees']; ?>
												</td>
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['forward_fees']; ?>
												</td>
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['online_service_fees']; ?>
												</td>
												<td>
												<?php echo $ReadmFee['ReadmStudentsFee']['total_fees']; ?>
												</td>
												
											<?php } ?>
											<?php } ?>
											</tbody>
											<tfoot>
											</tfoot>
										</table></div></div></div>
										</div>
									</div>
								</div>
								<!-- /.row -->
								<div>
							</fieldset>	
							</div>
							</div>
							<?php
							} ?>
					</div>
					<div class="col-sm-12 col-sm-offset-0"  style="font-size: 24px;">
						<?php $fld = 'application_fee'; $lbl = __('Grand Total of Admission Fee Payment Amount'); $lblhi = __('प्रवेश शुल्क भुगतान राशि का कुल योग'); ?>
						<div class="row">
							<div class="col-xs-8"><?php echo $this->Form->label($fld, $lblhi.' ('.$lbl.')');?></div>
							<div class="col-xs-4"><?php echo $application_fee ;?>/-</div>
						</div>
					</div> 

					</div>
					 
				</div>
				   <div class="box-footer text-center"> 
                       <?php 
					   if(isset($student['Student']['challan_tid']) &&  $student['Student']['challan_tid'] != ""){ ?>
								<?php 
									if(isset($Application['Application']['is_lock&submitted']) && $Application['Application']['is_lock&submitted'] == 1 && !empty($Application['Application']['lock&submitted_date'])){
								?>
									<div class="">
									<p style="color:green;font-size:22px;">
									पेमेंट करने के बाद आप एप्लीकेशन फॉर्म पीडीफ़ प्रारूप में डाउनलोड कर लें। (After payment, you download the application's form pdf format.)
									</p>
										<img src="/rsos/img/new.jpg" height="40" alt="">
										<a href="<?php echo $this->Html->url(array('controller'=>'Admission','action'=>'downloadpdf',$student['Student']['id']));?>" 
										class="btn btn-success"><i class="fa fa-download"></i> Download Payment PDF</a>
									</div> 
							<?php } else{ ?>
								<button type="submit" class="btn btn-success">
									<i class="fa fa-inr"></i> 
									Make Payment
								</button>
                      		    <?php  
								
								if($Erequest > 0){ ?>
								<br/>
								<hr/>
								<img src="/rsos/img/new.jpg" height="30" alt="">
								In case your transaction got failed, To verify your payment, please click on <b>Verify  Your Payment</b> button.
								<br>
								यदि आपका लेन-देन विफल हो जाता है, तो अपना भुगतान सत्यापित करने के लिए, कृपया क्लिक करें
								<b>अपना भुगतान सत्यापित करें</b> बटन.

								<a href="<?php echo $this->Html->url(array('action'=>'verify_request', $enrollment));?>" class="btn btn-info" ><i class="fa fa-tick"></i> Verify Your Payment</a>
								<br/>
								<br/>
								<?php if( $isAlreadyRaisedRequest <= 0){ ?>
									<hr/>
									<img src="/rsos/img/new.jpg" height="30" alt="">
									In case you want to raise a request to admin for your transaction got failed, please click on <b>Verify  Your Payment</b> button.
										<br>
										यदि आप अपने लेन-देन के लिए व्यवस्थापक से अनुरोध करना चाहते हैं तो विफल हो गया, कृपया पर क्लिक करें
										<b>अपना भुगतान सत्यापित करें</b> बटन.
										<a href="<?php echo $this->Html->url(array('action'=>'raise_request', $enrollment));?>" class="btn btn-warning" ><i class="fa fa-tick"></i> Raise Payment Request For Payment</a>
								<?php }else{
									echo "<span style='color:green;font-size:20px;'>आपके विफल लेनदेन के लिए व्यवस्थापक से आपका अनुरोध सबमिट कर दिया गया है। (Your request to admin for your failer transaction has been submitted.)</span>";
								} ?>
  
									<p class="hideShowItem">

									यदि आपका लेन-देन विफल हो जाता है, तो आप पुन: समाधान के लिए अनुरोध कर सकते हैं(In case your transaction got failed, you can raise request for re-conciliation).
									<a href="<?php echo $this->Html->url(array('action' => 'raise_request')); ?>" class="btn btn-danger"><i
												class="fa fa-upload"></i> Raise Request</a>
											
											
										
										
										<p>&nbsp;</p>
										
										
										<!-- ISSUES LIST RAISED BY THIS USER STARTS HERE -->
										
										<div class="box hideShowItem" style="border-top: 1px solid #3ab9ce">
													<div class="box-header">
													<h3 class="box-title">Previous Requests</h3>
													</div>
													<!-- /.box-header -->
													<div class="box-body table-responsive no-padding">
														<table class="table table-hover">
													
															<thead>
																<tr>
																	<th>S.No.</th>
																	<th>Request No.</th>
																	<th>Issue Type</th>
																	<th>Current Status</th>
																	<th>Issue Raised On</th>
																</tr>
															</thead>
															
															<?php
																if(isset($paymentIssueDetails)){
																	echo "<tbody>";
																	$count = 1;
																	foreach($paymentIssueDetails as $value){
																		$slag = $value['PaymentIssue'];
																		$issueArray = Configure::read('Site.payment_issues');
																		$issueName = $issueArray[$slag['issue_type']];
																		?>
																		
																	<tr>
																		<td style="text-align:center;"><?php echo $count;?>.</td>
																		<td style="text-align:left;"><?php echo "SR-". $slag['id'];?></td>
																		<td style="text-align:left;"><?php echo $issueName;?></td>
																		<td style="text-align:left;"><?php echo ($slag['status'] == 0) ? '<span class="label label-danger">Raised</span>' : '<span class="label label-success">Fixed</span>';?></td>
																		<td style="text-align:left;"><?php echo date("d-m-Y H:i a", strtotime($slag['created']));?></td>
																	</tr>
																		
																	<?php
																	
																	
																	$count++;
																	}
																	echo "</tbody>";
																} else {
																	echo "<tr><td colspan=6 align='center'> - No Record Found - </td></tr>";
																}
															?>
														</table>
													</div>
												</div>
										
										
										
												</p>
					
									<!-- ISSUES LIST RAISED BY THIS USER ENDS HERE -->
				
				
				
						
						<?php 
					}
				}
			}else{
				 
				 ?>

<button type="submit" class="btn btn-success">
									<i class="fa fa-inr"></i> 
									Make Payment
								</button>
								
			<?php }
					?>
					 
					 <?php  if($Erequest > 0 && $student['Student']['challan_tid'] == ""){ ?>
						<br/>
								<hr/>
								<img src="/rsos/img/new.jpg" height="30" alt="">
								In case your transaction got failed, To verify your payment, please click on <b>Verify  Your Payment</b> button.
								<br>
								यदि आपका लेन-देन विफल हो जाता है, तो अपना भुगतान सत्यापित करने के लिए, कृपया क्लिक करें
								<b>अपना भुगतान सत्यापित करें</b> बटन.

								<a href="<?php echo $this->Html->url(array('action'=>'verify_request', $enrollment));?>" class="btn btn-info" ><i class="fa fa-tick"></i> Verify Your Payment</a>
								<br/>
								<br/>
								<?php if( $isAlreadyRaisedRequest <= 0){ ?>
									<hr/>
									<img src="/rsos/img/new.jpg" height="30" alt="">
									In case you want to raise a request to admin for your transaction got failed, please click on <b></b> button.
										<br>
										यदि आप अपने लेन-देन के लिए व्यवस्थापक से अनुरोध करना चाहते हैं तो विफल हो गया, <b>  कृपया पर क्लिक करें
										</b> बटन.
										<a href="<?php echo $this->Html->url(array('action'=>'raise_request', $enrollment));?>" class="btn btn-warning" ><i class="fa fa-tick"></i> Raise Payment Request For Payment</a>
								<?php }else{
									echo "<span style='color:green;font-size:20px;'>आपके विफल लेनदेन के लिए व्यवस्थापक से आपका अनुरोध सबमिट कर दिया गया है। (Your request to admin for your failer transaction has been submitted.)</span>";
								} ?>
						<?php }
					?>
			
					</div>
							
			
			
</div>

 <?php echo $this->Form->end();?> 




<?php 
	echo $this->Html->script(array('plugins/jQuery/jquery-2.2.3.min.js'));
	echo $this->fetch('script');
	echo $this->Html->script(array('https://code.jquery.com/ui/1.12.1/jquery-ui.js'), array('inline' => true));
	echo $this->Html->css('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array('inline' => true));
	echo $this->Html->script(array('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js'), array('inline' => true));
	echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css', array('inline' => true));
	echo $this->Html->scriptStart(array('inline' => true));
?>

var validForm = false;
$(function(){ 

	

	$("#<?php echo $model;?>RegistrationFeeForm").on('submit',function(){ 

 

	

		if(validForm) return true; 
		var mc =  $.confirm({ 
			title: 'Pay <i class="fa fa-inr"></i> <?php echo $application_fee ;?>',
			content: 'Are you want to sure to make payment?',
			buttons: {
				sayMyName: {
					text: 'Proceed',
					btnClass: 'btn-warning',
					action: function () {
						validForm  = true;
						$("#<?php echo $model;?>RegistrationFeeForm").submit();
					}	
					
				},
				later: function () {
					// do nothing.
				}
			}
						
		});
		return false;
	}); 
}); 

<?php echo $this->Html->scriptEnd();?> 
