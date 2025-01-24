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
			<span style=" font-size:28px" class="font">{{$title}}</span>
		</div>

		<table border="1" style="width:100%;">
			<thead>
				<tr>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Sr.No.</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Exam Center Fixcode</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Course</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Subject</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;SSO ID</th>
				  	<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Examiner Name</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;	Mobile</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;	Total Students Appearing</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Total Copies of the subject</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Total Absent</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Total NR</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Date Of Allotment</th>
				</tr>
			<thead>	
			<tbody>
				<?php 
				if(!empty($result)){ 
				$i = 1;
				foreach($result as $data) {
				?>
				<tr>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ $i }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$examiner_list[@$data->examcenter_detail_id] }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$course[@$data->course_id] }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->subject_name }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->ssoid }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->name }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->mobile }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->total_students_appearing }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->total_copies_of_subject }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->total_absent }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->total_nr }}</td>
                         
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;@php  echo date("d-m-Y h:i:sa", strtotime($data->allotment_date));  @endphp</td>
				</tr>
				<?php $i++; } } ?>
			<tbody>	
		</table>
	</body>
</html>


 



