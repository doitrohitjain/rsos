<style>
	.centerLabel{	
		font-size: 20px;
	}

	 table.bottomBorder td, 
  table.bottomBorder th { 
    border-bottom: 1px solid lightgray; 
    padding: 5px; 
    text-align: left;
  }
	fieldset.scheduler-border {
	border: 1px lightgray solid !important;
	padding: 0 1em 1em !important;
	margin: 0 0 0 0 !important;
	-webkit-box-shadow: 0px 0px 0px 0px lightgray;
	box-shadow: 0px 0px 0px 0px lightgray;
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
		font-family:  Arial, sans-serif;
		float: none;
	}
	.pad, .box-title{
	margin-top:10px;
	}
	.page-header {
		padding-bottom:0px !important;
		margin:0px !important;
		border:0px  !important;
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
		border-bottom: 1px solid lightgray; 
		border-top: 1px solid lightgray !important; 
	}
	.botomBorder{ 
		border-bottom: 2px solid lightgray !important; 
	}
	  .new-page {
		page-break-before: always;
	  }
	   table.bottomBorder { 
    border-collapse: collapse; 
  }
  table.bottomBorder td, 
  table.bottomBorder th { 
    border-bottom: 1px solid lightgray; 
    padding: 5px; 
    text-align: left;
  }

  .font{
 font-family: Arial, sans-serif;
 }
</style>
<table  cellspacing="0" style="width:100%" class="bottomBorder font" >
		<tr>
			<td style="width:10%">
			<p><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px;  border-radius: 10px" alt="image upload button"/></p>
			</td>
			<td style="text-align:center; vertical-align:middle">
			<p style="font-size:22px;"><b>RAJASTHAN STATE OPEN SCHOOL</b></p>

			<p style="font-size:16px;">	[ <?php 
						if($course == 10){
						echo 'SECONDARY';
						}
					   if($course == 12){
					   echo 'SENIOR SECONDARY';
					}
			     ?> ] EXAMINATION CENTER NOMINAL ROLL (EXAMINATION , <?php echo $exam_session[$stream];?> )</p>
			</td>
		</tr>
</table>
      @php $finalData = $final_data; @endphp
                 @foreach($finalData as $subjectscount)

				<div class="box box-primary">
					<div class="box-header with-border">
						<div class="row">
							<div class="text-left">
									<div class="bottomBorder font" style="font-size:14px;">
										<b>Centre: [
										    {{ @$subjectscount['cent_code'] }}
										] 
										  {{ @$subjectscount['cent_name'] }}
										</b>
									</div>
									<table class="table table-responsive bottomBorder font" width="100%" >
												</br>
												<thead>
													<tr>
													<th class="bothBorder"style="font-size:14px;width:3%;" >Sr.No</th>
													<th class="bothBorder"style="font-size:14px;width:10%;">Enrollment</th>
													<th class="bothBorder"style="font-size:14px;width:18%;">Candidate Name</th>
													<th class="bothBorder"style="font-size:14px;width:15%;">Father Name</th>
													<th class="bothBorder"style="font-size:14px;width:15%;">Mother Name</th>
													<th class="bothBorder"style="font-size:14px;width:14%;">DOB</th>
													<th class="bothBorder"style="font-size:14px;width:5%;">Category</th>
													<th class="bothBorder"style="font-size:14px;width:20%;">Examination Subject</th>
													</tr>
												</thead>
											   	<tr>
														<td colspan="8" style="font-size:12px;"><b>AI Code : {{ $subjectscount['aicode']}} </b></td>
													</tr>
													@php $i=1; @endphp
													@foreach($subjectscount['examsubject'] as $key => $value)
														<tr>
															<td style="font-size:14px;width:3%;text-transform: uppercase;">{{@$i}}</td>
															<td style="font-size:14px;width:10%;text-transform: uppercase;">{{@$value['enrollment']}}</td>
															<td style="font-size:14px;width:18%;text-transform: uppercase;">{{@$value['name']}}</td>
															<td style="font-size:14px;width:15%;text-transform: uppercase;">{{@$value['father_name']}}</td>
															<td style="font-size:14px;width:15%;text-transform: uppercase;">{{@$value['mother_name']}}</td>
															<td style="font-size:14px;width:14%;">{{@$value['dob']}}</td>
															<td style="font-size:14px;width:5%;text-transform: uppercase;">{{@$categorya[$value['category_a']]}}
															</td>
															<td style="font-size:14px;width:20%;"> 
																<?php
																foreach(@$value['examsubject'] as $key => $values){ 
																	echo @$subject_list[$values['subject_id']] . '&nbsp;';
															} ?>
															</td>														
														</tr>
														 @php $i++; @endphp
												@endforeach
												
									</table>
								<div style = "display:block; clear:both; page-break-after:always;"></div>
							</div>
						</div>
					</div> 
				</div>
				@endforeach
			</section>
		</div>
	</div>
	