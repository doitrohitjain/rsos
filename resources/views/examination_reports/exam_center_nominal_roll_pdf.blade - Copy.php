<?php 
use App\Component\CustomComponent;
$custom_component_obj = new CustomComponent;
?>

@if($courseid == 10);
<!--  10Th case -->
<div class="row">
	<div class="col-md-12">
		<section class="invoice">
			<!-- title row -->
			<div class="row">
				<table cellspacing="0" style="width:100%;height:25px;" class="font">
					<tbody>
						<tr>
							<td style="width:10%">
								<p><img src="http://10.68.181.236/lrsos/public/app-assets/images/favicon/administrator.png" style="width: 70px; height: 70px; border-radius: 10px;" alt="image upload button"></p>
							</td>
							<td style="text-align:center; vertical-align:middle">
								<span style="font-size:24px;">RAJASTHAN STATE OPEN SCHOOL
								<span><br>
									<span style="font-size:14px">
									[SECONDARY] EXAMINATION CENTER NOMINAL ROLL (EXAMINATION , <?php echo $examDates[$stream];?> )
									</span></b></b>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="col-xs-12" style="">
				<div class="row">
					<table class="font" style="width:100%;border-top:1px #ccc solid !important;border-left:1px #ccc solid !important;">
							<tr>
								<td colspan='3'><span style="width:100%;font-weight:bold;font-size:14px">Center Name: {{ @$examCenterDetail->college_name; }}</span></td>
							</tr>
							<tr>
								<td colspan="8"><hr></td>
							</tr>
					</table>
				</div>
			</div>
			<!-- Table row -->
			<div class="box box-primary" >
				<div class="box-header">
					<div class="row">
						<div class="text-left">
							<table class="table table-responsive font" width="100%" style="font-size: 12px;border-left:1px #ccc solid !important;">
								<tr>
									<th style="text-align:left;width: 10%;">Sr.No</th>
									<th style="text-align:left;width: 10;">Enrollment</th>
									<th style="text-align:left;width: 15%;">Candidate Name</th>
									<th style="text-align:left;width: 15%;">Father Name</th>
									<th style="text-align:left;width: 15%;">Mother Name</th>
									<th style="text-align:left;width: 10%;">DOB</th>
									<th style="text-align:left;width: 10%;">Category</th>
									<th style="text-align:left;width: 15%;">Examination Subject</th>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php
								// @dd($final_data); 
								if(isset($final_data[10]) && !empty($final_data[10])){
								$i=1;	
								foreach($final_data[10] as $data){
								if($i==1){	
								?>
								<tr>
									<th colspan='8' width="100%"  style="text-align:left;font-size: 12px;">AI Code : <?php echo (isset($data['ai_code'])) ? $data['ai_code'] : ''; ?></th>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php } ?>
								<tr>
									<td  style="text-align:left;text-transform: uppercase;">{{ $i }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['enrollment'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['name'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['father_name'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['mother_name'] }}</td>
									<td  style="text-align:left">{{ date("d-m-Y",strtotime($data['dob'])) }}</td>
									<td  style="text-align:left;text-transform: uppercase;"><?php if(isset($categorya_list[$data['category']])) { echo $categorya_list[$data['category']]; } else { echo "-"; } ?></td>
									<td  style="text-align:left;text-transform: uppercase;">
									<?php 
									if(isset($data['supp_id']) && !empty($data['supp_id'])){
										$subject_list = $custom_component_obj->getSuppSubjectbySuppId($data['supp_id']); 
									}
									?>
									</td>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php $i++; }  } else { ?>
								<tr>
									<td colspan="8" style="text-align:center;width:100%">Data Not Found</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
				</div> 
			</div>
		</section>
	</div>
</div>
@elseif($courseid == 12);
<!--  10Th case -->
<!--  12Th case -->
<div class="row">
	<div class="col-md-12">
		<section class="invoice">
			<!-- title row -->
			<div class="row">
				<table cellspacing="0" style="width:100%;height:25px;" class="font">
					<tbody>
						<tr>
							<td style="width:10%">
								<p><img src="http://10.68.181.236/lrsos/public/app-assets/images/favicon/administrator.png" style="width: 70px; height: 70px; border-radius: 10px;" alt="image upload button"></p>
							</td>
							<td style="text-align:center; vertical-align:middle">
								<span style="font-size:24px;">RAJASTHAN STATE OPEN SCHOOL
								<span><br>
									<span style="font-size:14px">
									[SENIOR SECONDARY] EXAMINATION CENTER NOMINAL ROLL (EXAMINATION , <?php echo $examDates[$stream];?> )
									</span></b></b>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="col-xs-12" style="">
				<div class="row">
					<table class="font" style="width:100%;border-top:1px #ccc solid !important;border-left:1px #ccc solid !important;">
							<tr>
								<td colspan='3'><span style="width:100%;font-weight:bold;font-size:14px">Center Name: {{ @$examCenterDetail->college_name; }}</span></td>
							</tr>
							<tr>
								<td colspan="8"><hr></td>
							</tr>
					</table>
				</div>
			</div>
			<!-- Table row -->
			<div class="box box-primary" >
				<div class="box-header">
					<div class="row">
						<div class="text-left">
							<table class="table table-responsive font" width="100%" style="font-size: 12px;border-left:1px #ccc solid !important;">
								<tr>
									<th style="text-align:left;width: 10%;">Sr.No</th>
									<th style="text-align:left;width: 10;">Enrollment</th>
									<th style="text-align:left;width: 15%;">Candidate Name</th>
									<th style="text-align:left;width: 15%;">Father Name</th>
									<th style="text-align:left;width: 15%;">Mother Name</th>
									<th style="text-align:left;width: 10%;">DOB</th>
									<th style="text-align:left;width: 10%;">Category</th>
									<th style="text-align:left;width: 15%;">Examination Subject</th>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php
								// @dd($final_data); 
								if(isset($final_data[12]) && !empty($final_data[12])){
								$i=1;	
								foreach($final_data[12] as $data){
								if($i==1){	
								?>
								<tr>
									<th colspan='8' width="100%"  style="text-align:left;font-size: 12px;">AI Code : <?php echo(isset($data['ai_code'])) ? $data['ai_code'] : ''; ?></th>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php } ?>
								<tr>
									<td  style="text-align:left;text-transform: uppercase;">{{ $i }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['enrollment'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['name'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['father_name'] }}</td>
									<td  style="text-align:left;text-transform: uppercase;">{{ $data['mother_name'] }}</td>
									<td  style="text-align:left">{{ date("d-m-Y",strtotime($data['dob'])) }}</td>
									<td  style="text-align:left;text-transform: uppercase;"><?php if(isset($categorya_list[$data['category']])) { echo $categorya_list[$data['category']]; } else { echo "-"; } ?></td>
									<td  style="text-align:left;text-transform: uppercase;">
									<?php 
									if(isset($data['supp_id']) && !empty($data['supp_id'])){
										$subject_list = $custom_component_obj->getSuppSubjectbySuppId($data['supp_id']); 
									}
									?>
									</td>
								</tr>
								<tr>
									<td colspan="8"><hr></td>
								</tr>
								<?php $i++; }  } else { ?>
								<tr>
									<td colspan="8" style="text-align:center;width:100%">Data Not Found</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
				</div> 
			</div>
		</section>
	</div>
</div>
@endif
<!-- 12th case -->

<?php // echo $this->Html->script(array('https://cdn.rawgit.com/imsky/holder/master/holder.js'),array('inline'=>false));?>
<style>
	thead, tfoot { display: table-header-group; }
	tr { page-break-inside: avoid; }
	.new-page {
		page-break-before: always;
	  }
	.centerLabel{	
		font-size: 20px;
	}
	fieldset.scheduler-border {
	// border: 1px #ccc solid !important;
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
	
	
	
	/*
	.bothBorder{ 
		border-bottom: 1px solid #000; 
		border-top: 1px solid #000 !important; 
	}
	.botomBorder{ 
		border-bottom: 2px solid #000 !important; 
	}
	*/
	
	.bothBorder{ 
		border-top: 2px solid #ccc !important; 
	}
	.bottomBorder{ 
		border-bottom: 2px solid #ccc !important; 
	}
	
	.font{
		font-family: Arial, sans-serif;
	}
</style>