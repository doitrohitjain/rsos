<?php 
$page=1;
$ncounter = 1;
if(isset($students) && !empty($students)){
		
	foreach($students as $examcenterkey => $subjectwise)
	{ 
		if(empty($subjectwise['students']) && empty($subjectwise['supplementary']))
		{
			continue;
		}
		$ncounter++;
	?>
			
					 <?php 
					if(isset($subjectwise) && !empty ($subjectwise) && count($subjectwise) > 0)
					{ 
							$temp = 1;$counter = 0;
							foreach($subjectwise as $skey => $studentarr)
							{
								
								if(isset($studentarr) && !empty ($studentarr) && count($studentarr) > 0)
								{ 
									foreach($studentarr as $akey => $student){
									$result = $this->Custom->getStudentResult($student['StudentAllotment']['enrollment'],$subjectDetail['Subject']['id']);
											if(in_array($subjectDetail['Subject']['id'],$practicalsubjects12) && $skey == 'supplementary' && $result == 666){ 
													continue;
											}else{	
									if($temp == 1){
										//$page++;
										?>
										<table width="100%" style="margin:0px 0px 0px 0px;" class="new-page">
			<?php /*if($counter > 1){ ?>
				<table width="100%" style="margin:0px 0px 0px 0px;" border=0 class="new-page">
			<?php }else{ ?>
				<table width="100%" style="margin:0px 0px 0px 0px;" border=0>
			<?php }*/ ?>
				<thead style="width:100%;">
					<tr>
						<th colspan="8" style="width:100%;">
							<div style="width:100%;">
								<div style="width:100%;">
								<span style="tttext-align:center;font-size:15px;background:white;"><b>SUB. 
										<?php
										
										$str = $subjectDetail['Subject']['name'];
											echo $str;//.'[Theory]';
										?></b>
									</span>									
									&nbsp;&nbsp;
									<span style="tttext-align:center;font-size:15px;background:white;"><b>FC: 
										<?php 
											echo $examCenterDetail[$examcenterkey]['fixcode'];
										?></b>
									</span>
									&nbsp;&nbsp;
									<span style="tttext-align:left;font-size:15px;background:white;"><b><?php 
										if(in_array($subjectDetail['Subject']['id'],array_keys($subjectDetail10))){
												echo $examCenterDetail[$examcenterkey]['ecenter10']."-".$examCenterDetail[$examcenterkey]['cent_name'];
											}else{
												echo $examCenterDetail[$examcenterkey]['ecenter12']."-".$examCenterDetail[$examcenterkey]['cent_name'];
											}
									?></b>
									</span>
									&nbsp;&nbsp;
									<span style="tttext-align:center;font-size:15px;background:white;"><b>
										<?php
										 
										echo $page++;
										?></b>
									</span>
									<!--&nbsp;&nbsp;
									<span style="tttext-align:center;font-size:10px;background:white;"><b>EXAM : 
										<?php
										 
										//echo $examDates[$stream];
										?></b>
									</span>-->
									
								</div>
								
								<!-- <div class="col-xs-4 text-center">
									<span style="tttext-align:center;font-size:10px;background:white;"><b>SUBJECT : 
										<?php 										
										$str = $subjectDetail[$subjectval];
											echo $str.'[Theory]';
										?></b>
									</span>
								</div> -->
								<!-- <div class="col-xs-3 text-right">
									<span style="tttext-align:right;font-size:10px;"><b>Exam Date: <?php echo $this->Custom->getExamDate($stream,$subjectval);?></b></span>
								</div> --> 
							</div>
						</th>
					</tr>
				</thead> 
				<tbody style="width:100%;">
										<?php
										//$page++;
									}
								?>	
								<?php 
								//$counter == 0 || 
								if($counter%2 == 0){ ?>
								<tr> 
								<?php } ?>
										<td align="center" style="height:115px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;border:1px solid red;">
											<span style="font-size:20px;"><b><?php echo $student['StudentAllotment']['enrollment'];?></b></span><br />
											<span style="font-size:20px;"><b><?php echo $student['StudentAllotment']['fixcode'];?></b></span>
										</td>
								<td align="center" style="font-size:24px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;"><span><b><?php echo $student['StudentAllotment']['fixcode'];?></b></span>
										</td>
										
											<?php
											$totstudents=0;
											if(isset($subjectwise['students']))
											{
												$totstudents += count($subjectwise['students']);
											}
											if(isset($subjectwise['supplementary']))
											{
												$totstudents += count($subjectwise['supplementary']);
											}
											if($totstudents == 1){?>
												<td align="center" style="height:115px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">&nbsp;</td>
												<td align="center" style="font-size:18px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">&nbsp;</td>
											<?php }
											$counter++;
												$temp++;
												if($temp == 29)
												{
													//$page++;
													$temp = 1;
													echo "</tbody></table>";
												}
											}
											
										}
										$totnewstudents=0;
											if(isset($subjectwise['students']))
											{
												$totnewstudents += count($subjectwise['students']);
											}
											if(isset($subjectwise['supplementary']))
											{
												$totnewstudents += count($subjectwise['supplementary']);
											}
										if($totnewstudents%28 == 1){?>										
										<td align="center" style="height:115px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">&nbsp;</td>
										<td align="center" style="font-size:18px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">&nbsp;</td>
										</tr>	
										<?php }
									}else{
										continue;
									} ?>
								<?php 
								//
								if($counter%2 == 0){ ?>
								</tr>  
								<?php } ?>
						<?php } ?>								
					<?php } ?>
									 
				
			
			<!-- <div class="new-page">&nbsp;</div> -->
	<?php } ?>
<?php } ?>

<style>
	.new-page {
		page-break-before: always;
	}
	thead{
		border: 0px !important;
	}
	tr {
		page-break-inside: avoid;
	}
	.settd{
		height:100px;
		width:250px;
		text-align:center;
	}
	body, html{
		margin:0px 0px 0px 0px;
		padding: 0px 0px 0px 0px;
	}
</style>

