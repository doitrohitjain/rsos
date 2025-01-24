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
			<span style=" font-size:28px" class="font">{{$title.' '.$exam_year[$currentexameyear]}}</span>
		</div>

		<table border="1" style="width:100%;">
			<thead>
				<tr>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Sr.No.</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Examiner Name</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;SSO</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Mobile</th>
					<th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Designation</th>
					{{-- <th style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;Exam Month</th> --}}
				</tr>
			<thead>	
			<tbody>
				<?php 
				if(!empty($result)){ 
				$i = 1;
				foreach($result as $data) {
				?>
				<tr>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$i }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->name }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->ssoid }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->mobile }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->designation }}</td>
					{{-- <td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$current_sesions[$data->exam_month] }}</td> --}}
				</tr>
				<?php $i++; } }else{ ?>
                 <tr>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$i }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->name }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->ssoid }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->mobile }}</td>
					<td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{ @$data->designation }}</td>
					{{-- <td style="font-size:16px; border: 1px solid #dddddd; text-align: left; padding: 8px;"> &nbsp;{{@$current_sesions[$data->exam_month] }}</td> --}}
				</tr>
				 <?php }?>
			<tbody>	
		</table>
	</body>
</html>


 



