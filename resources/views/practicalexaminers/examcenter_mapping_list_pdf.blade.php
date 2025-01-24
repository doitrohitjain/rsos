<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
		<style type="text/css">
			table{
				 border-collapse: collapse;
				border-spacing: 0;
				font-family: arial, sans-serif;
			}
			.cc51 {
			  white-space: nowrap;
			  text-align: center;
			  vertical-align: middle;
			}
			.cc55 {
			  display: inline-block;
			  vertical-align: middle;
			}
			.font{
				font-family: Arial, sans-serif;
			}
		</style>
	</head>
	<body style="margin-top:-5%,margin-bottom:0%">
		<div class="cc51"> 
			 <div class="cc55"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px; height: 60px; border-radius: 10px" alt="image upload button"/>
			</div>                 
			<span style=" font-size:28px" class="font">Examiner Mapping List</span>
		</div>

		<table border="1" style="width:100%;">
			<thead>
				<tr>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Sr.No.</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Academic Year</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Exam Center Name</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Examiner SSO ID</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Course</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Stream</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Subject</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Is Lock & Submitted</th>
				</tr>
			<thead>	
			<tbody>
				@php  
				$i= 1; @endphp
				@foreach(@$master as $data)
				<tr>
					<td>{{ $i; }}</td>
					<td><?php if(isset($exam_year_arr[@$current_exam_year]) && isset($exam_month_arr[@$current_exam_month])){ echo $exam_year_arr[@$current_exam_year]."/".$exam_month_arr[@$current_exam_month]; }else { echo "-"; } ?></td>
					<td><?php if(isset($examcenter_list[@$data->examcenter_detail_id])){ echo $examcenter_list[@$data->examcenter_detail_id]; }else { echo "-"; } ?></td>
					<td>{{ @$data->ssoid }}</td>
					<td>{{ @$data->course }}th</td>
					<td>{{ @$data->stream }}</td>
					<td><?php if(isset($subject_list[$data->subject_id])){ echo $subject_list[$data->subject_id]; }else { echo "-"; } ?></td>
					<td>-</td>
				</tr>
				@php  $i++; @endphp
				@endforeach
			<tbody>	
		</table>
	</body>
</html>


 



