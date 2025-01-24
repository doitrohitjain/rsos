<?php use App\Component\CustomComponent; ?>
<style>
	thead, tfoot { display: table-header-group; }
	.centerLabel{	
		font-size: 20px;
	}
	fieldset.scheduler-border {
	border: 1px lightgray solid !important;
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
	.new-page {
		page-break-after: always;
	  }
	  table{
		 border-collapse: collapse;
    border-spacing: 0;
    border-color: lightgray;
    border-left: 0px;
	border-right: 0px;
  
	}
.font{
 font-family: Arial, sans-serif;
 }
td{
	height:40px;
	padding: 2px 2px 2px 2px ;
	border-left: 0px;
	border-right: 0px;
}
th{
	height:40px;
	border-left: 0px;
	border-right: 0px;
}
</style>

<div class="row" style="margin-left:13px;margin-right:13px;">
	<div class="row"> 
		<div class="col-xs-12 page-header">
			<div class="col-xs-2"> 
				<h2 class="pull-center" style="margin-top:0px;margin-bottom:0px;">
					<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 70px;  border-radius: 10px"align="left"/>	 							
				</h2>
			</div>
			<div class="col-xs-10">
				<h2 class="text-center font" style="margin-top:0px;margin-bottom:0px;">
					<center>RAJASTHAN STATE OPEN SCHOOL</center>
				</h2>
				<h4 class="font">
					SUBJECT WISE SIGNATURE ROLL (EXAMINATION ,<?php 
						if(isset($exam_session[$stream])){
							echo $exam_session[$stream];
						}
					?>)
				</h4>
			</div>
		</div>  
	</div>
</div>
@php $finalData = $final_data; @endphp
@foreach($finalData as $subjectscount)
		<table class="new-page font" width="100%" border="1px">
			<tbody>
				<tr style="width:100%">
					<td class="font" colspan="2">&nbsp;Center Code: {{ @$subjectscount['cent_code'] }}</td>
					<td class="font" colspan="4" style="text-align: right;">SUBJECT : <b>{{ $subjectList[$subjectscount['subject_id']]}} [Theory]</b></td>
					<td class="font" colspan="2" style="text-align: right;">&nbsp;&nbsp;Exam Date: <?php 
														$custom_component_obj = new CustomComponent;
														$exam_date = $custom_component_obj->getExamDate($stream,$subjectscount['subject_id']);
														echo $exam_date;
														?></td>
				</tr>
				<tr>
				  <td colspan="12">&nbsp;Center Name: {{ @$subjectscount['cent_name'] }}</td>
				</tr> 
				<tr>
					<th width="3%">&nbsp;Sr.No</th>
					<th width="12%">Roll No.</th>
					<th width="25%">Name</th>
					<th width="10%">Signature</th>
					<th width="3%">&nbsp;Sr.No</th>
					<th width="12%">Roll No.</th>
					<th width="25%">Name</th>
					<th width="10%">Signature</th>
				</tr> 
			
				@php $counter = 0; $showCounter = 1;@endphp 
				<tr>
					@php $type = "studentData";@endphp
					@if(@$subjectscount[$type] && count($subjectscount[$type]))
						@foreach($subjectscount[$type] as $student)
							@if($counter%2 == 0)
									</tr>
									<tr>
								@endif
								<td width="3%" style="text-transform: uppercase;">&nbsp;<?php echo $showCounter;?></td>
								<td width="12%" style="text-transform: uppercase;">&nbsp;{{@$student->enrollment}}</td>
								<td width="25%" style="text-transform: uppercase;">&nbsp;{{@$student->name}}</td>
								<td width="10%" style="text-transform: uppercase;">&nbsp;................</td>
							@php $showCounter++;$counter++; @endphp
						@endforeach 
					@else
						<tr><td colspan="8"><b>Nil<b></td></tr>
					@endif
					<tr><td colspan="8"><b>&nbsp;Supplementary Students<b></td></tr>
						<!-- 25092022 updated -->
					@php $type = "suppStudentData"; $counter = 0;@endphp
					@if(@$subjectscount[$type] && count($subjectscount[$type]))
						@foreach($subjectscount[$type] as $student)
							@if($counter%2 == 0)
									</tr>
									<tr>
								@endif				
							<td width="3%" style="text-transform: uppercase;">&nbsp;<?php echo $showCounter;?></td>
							<td width="12%" style="text-transform: uppercase;">&nbsp;{{@$student->enrollment}}</td>
							<td width="25%" style="text-transform: uppercase;">&nbsp;{{@$student->name}}</td>
							<td width="10%" style="text-transform: uppercase;">&nbsp;................</td>
						
							@php $showCounter++;$counter++; @endphp
						@endforeach 
					@else
						<tr><td colspan="8"><b>&nbsp;Nil<b></td></tr>
					@endif
				</tr> 
			</hr>
			</tbody>
		</table>
	@endforeach


