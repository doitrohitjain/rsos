<html>
<style>
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
		font-family: Arial;
		float: none;
	}
	.pad, .box-title{
		margin-top:10px;
	}
	.page-header {
		padding-bottom:1px !important;
		margin:-20px !important;
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
	#signaturetbl tr td{
		height:25px;
	}
	
	h4,h5{
		text-align:center;
	}
	@media print {
  h4 {
    display: table-header-group;
  }
  h5 {
    display: table-header-group;
  }
  .cc51 {
  font-size: 15px;
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}

.page-break {
		page-break-after: always;
		page-break-inside:avoid;
	}
.font{
 font-family: Arial, sans-serif;
 }

</style>
<body class="font">
<div class="row">
<div class="col-md-12">
<section class="invoice" style='font-family: Arial;'>
<!-- title row -->
<div class="row page-header new-page" style="margin-left:10px;margin-right:10px; border-bottom:2px solid;">
	<div class="col-xs-2">
		<h2 class="text-center">
			<div id="container">
			 <div id="image" style=" margin-left:90px;">
        <img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px;  border-radius: 10px"align="left"/>
    </div>
   </div>
		</h2>
	</div>
	<div class="col-md-12">
	<div class="text-center">
				<span class='font' style="margin-left:110px; font-size:25;"><b>RAJASTHAN STATE OPEN SCHOOL</b><br>
				<span style="font-size:16px;margin-left:60px;" class='font'><?php
							if(isset($course) && $course == 10){
								echo '| SECONDARY '; 
							}else if(isset($course) && $course == 12){
								echo '| SENIOR SECONDARY';
							}						
						?>
						| ATTENDANCE ROLL(EXAMINATION
						 <?php 
				            if(isset($exam_session[$stream])){
				              echo $exam_session[$stream];
				            }
				          ?>
		)</span></span>
			</div>
	</div>
	<br>
	<!-- /.col -->
</div>

<!-- Table row -->
<div class="box box-primary">

	<div class="box-header with-border">
		<div class="row pad form-group-lg">
			<div class="text-left"> 
					<h5>
						@if(@$course == 10)
            @foreach($master as $key => $data)
						@if(@$key == 0)  
						 <span style="font-size:16px;" > Centre: {{@$data['ecenter10']}} | {{@$data['cent_name']}}</span>
						@endif
            @endforeach
	          @elseif(@$course == 12)
	          @foreach($master as $key => $data)
	           	@if(@$key == 0)  
						   <span style="font-size:16px;"> Centre: {{@$data['ecenter12']}} | {{@$data['cent_name']}}</span>
						@endif
           @endforeach
						@endif
				</h5>

				<?php if(isset($master) && !empty ($master)){
				$i=1; 
				foreach($master as $key => $aicodeStudent){ ?>

					<!-- <div class="col-sm-12 well well-sm" style="margin-top:20px; width:100%; border:2px; float:left;"> */
						<span class="centerLabel">
							AI Code : <?php echo $key; ?>
						</span>
					</div>-->
					
				
						<div style="width:100%;float:left;margin-left: 5px;border-bottom:2px solid;height:290px;">
						
							<table style="margin-top:1px;width:100%;">
								<tr>
									<td colspan="3">Enrollment No.: <b><?php echo @$aicodeStudent['enrollment']?></b></td>
								</tr>
								<tr >
									<td colspan="4">Candidate Name: <b style="text-transform: uppercase;"><?php echo (isset($aicodeStudent['name']) && $aicodeStudent['name'] != "") ? $aicodeStudent['name'] : $aicodeStudent['name'];?></b></td>
								</tr>
								<?php if(isset($aicodeStudent['examsubject']) && !empty($aicodeStudent['examsubject'])){ ?>
									<tr>
										<td >
											<table style="width:100%;" id="signaturetbl3">
												 <tr>
													<td style="vertcal-align:top; margin-bottom:10px;">
														@if(!empty($aicodeStudent['photograph']))  
														<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['photograph'])}}" width="60px" /> 
														@else 
														<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/studentuser.png')}}" width="60px" />
														@endif 
													</td>
													
													<!-- <td style="margin-bottom:10px;">
										
														@if(!empty($aicodeStudent['photograph']))
														<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['photograph'])}}" width="60px" />
														@else 
														<img alt="materialize logo" height="60px" src="{{asset('public/app-assets/images/studentuser.png')}}" width="60px" />
														@endif 
													</td> -->
												</tr>
												<tr>
													<!-- <td>
														@if(!empty($aicodeStudent['signature']))
														<img alt="materialize logo" height="30px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['signature'])}}" width="60px" />
														@else 
														<img alt="materialize logo" height="30px" src="{{asset('public/app-assets/images/studentsignature.png')}}" width="60px" />
														@endif 
													</td> -->
													
													<td style="margin-bottom:10px;">
														@if(!empty($aicodeStudent['signature']))  
														<img alt="materialize logo" height="30px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['signature'])}}" width="60px " />
														@else 
														<img alt="studentsignature" height="30px" src="{{asset('public/app-assets/images/studentsignature.png')}}" width="60px" />
														@endif 
													</td>
												</tr>
												
											</table>
										</td>
										<td style="padding-left:0px;vertical-align:top;">
											<table style="width:100%;margin-top: 1px;" id="signaturetbl" >
												<tr>
													<td style="text-align:center">Subject</td>
													<td style="text-align:center">Answer. Sheet No.</td>
													<td style="text-align:center">Photo ID Name</td>
													<td style="text-align:center">Photo ID Detail</td>
													<td style="text-align:center">Signature</td>
												</tr>
												<?php foreach($aicodeStudent['examsubject'] as $key => $value){ ?>
													<tr>
														<td style="text-align:center"><?php echo @$subject_list[$value['subject_id']]; ?></td>
														<td style="text-align:center">.....................</td>
														<td style="text-align:center">.....................</td>
														<td style="text-align:center">.....................</td>
														<td style="text-align:center">.....................</td>
													</tr>		
												<?php } ?>
												
											</table>
										</td>
									</tr>
									<span style="margin-left:860px; padding-top:270px;position: fixed;"><?php echo $i;?></span>
								<?php } ?>
							</table>
						
						</div>
						<?php if($i%4==0){
							?> 
							<div style = "display:block; clear:both; page-break-after:always;"></div>

					<?php }?>
				  <?php $i++;?>
				<?php }} ?>
			</div>
		</div>
	</div> 
</div>
</section>
</div>
</div>
</body>
</html>



