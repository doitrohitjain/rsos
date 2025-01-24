@include('elements.reportlogo') 
<div class="row">
	<div class="col-md-12">
		<section class="invoice">
			<!-- title row -->
			<div class="row">
				<div class="col-xs-2">
					<h2 class="page-header pull-center">
	
					</h2>
				</div> 
			</div>
			
			<!-- Table row -->
			<div class="box box-primary" style="margin-left:10px;margin-right:10px;">
				<div class="box-header with-border">
					<div class="row">
						<div class="text-left">
							<?php 
							$students =$master;
							if(isset($students) && !empty ($students)){ $counter = 0;  ?> 
									<?php foreach($students as $courseval => $student){  ?>
										  
											<?php  if($courseval == 12){ ?>
												<table class="table table-responsive new-page"  width="100%" style="overflow:auto !important;">
											<?php }else{ ?>
												<table class="table table-responsive new-page2"  width="100%" style="overflow:auto !important;">
											<?php } ?>
												<thead>
													<tr>
														<th colspan="10" style="text-align:left !important;"><b>Course : <?php 
															if($courseval == 10){
																echo 'SECONDARY';
															}else if($courseval == 12){
																echo 'SENIOR SECONDARY';
															}else{
																echo '-';
															}
														?></b></th> 
													</tr> 
													<tr style="font-size:12px;">
														<th width="2%" class="bothBorder">Sr.No</th>
														<th width="10%" class="bothBorder">Enrollment</th>
														<th class="bothBorder" style="width:18%;">Candidate Name</th>
														<th class="bothBorder" style="width:18%;">Father Name</th>
														<th class="bothBorder" style="width:18%;">Mother Name</th>
														<th class="bothBorder" style="width:10%;">DOB</th>
														<th class="bothBorder" style="width:5%;">Category</th>
														<th class="bothBorder" style="width:20%;">Examination Subject</th>
													</tr> 
												</thead>
												
										
										 
											<?php 
											$showCounter=1;
											   // 
											// ksort($student);
											foreach($student as $courseval => $actualStudent){ 
											
											//dd($actualStudents);
											
											$coutnerTemp=0; //echo $courseval;echo '--------';?>
											 
													<tr style="font-size:12px;">
														<td  width="2%"><?php echo $showCounter;?></td>
														<td  width="10%"><?php echo $actualStudent->enrollment; ?></td>
														<td  width="18%"><?php echo $actualStudent->name;?></td>
														<td width="18%"><?php echo $actualStudent->father_name;?></td>
														<td width="18%"><?php echo $actualStudent->mother_name;?></td>
														<td width="10%"><?php 
														$nyr = substr($actualStudent->enrollment,-6,-4);
														if(isset($actualStudent->dob) && $actualStudent->dob != ""){
															
															
															
															if($nyr > 16)
															{
																$sdobarr = explode('-',$actualStudent->dob);													
																$sdob = $sdobarr[2]."-".$sdobarr[1]."-".$sdobarr[0];
																echo $sdob;
																
															}else{
																echo $actualStudent->dob;
															} 
														}
														?></td>
														<td width="5%">
															<?php 
																echo @$categorya[$actualStudent->application->category_a];
															?>
														</td >
														<td width="20%">
															<?php
															foreach(@$actualStudent->exam_subject as $key => $value){ 
																echo @$subject_list[$value->subject_id] . '&nbsp;';
															} ?>
														</td>														
													</tr>
												<?php $showCounter++;} //foreach actualStudents end ?>
												<tr>
													<td colspan="8" class="botomBorder"></td>
												</tr>
											 
										</table>
										 <?php  
											$counter++; 
										} //foreach students end 
										?>
							<?php }else{ ?>
								<table class="table table-responsive">
									<tr>
										<b>Nill</b>
									</tr>
								</table>
							<?php } ?>
						</div>
					</div>
				</div> 
			</div>
		</section>
	</div>
</div>
<style>
	thead, tfoot { display: table-header-group; }
	tr { page-break-inside: avoid; }
	.new-page {
		page-break-before: always;
	  }
	.centerLabel{	
		font-size: 10px;
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
		font-size: 50%;
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
		<script type='text/php'>
            if (isset($pdf)) 
            {               
                $pdf->page_text(60, $pdf->get_height() - 50, " Page {PAGE_NUM} / {PAGE_COUNT}", null, 12, array(0,0,0));
            }
</script>
