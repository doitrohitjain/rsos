@php $finalData = $final_data; @endphp
@foreach($finalData as $subjectscount)
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.87.0">
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
	
.font{
 font-family: Arial, sans-serif;
 }
body, html{
	margin:0px 0px 0px 0px;
	/* padding: 0px 0px 0px 0px; */
}
</style>
</head>
<body>


<table width="100%" style="margin:0px 0px 0px 0px;">
			<thead style="width:100%;">
					<tr class='font' style="margin-top: -2%;">
						<th colspan="8">
								<span style="font-size:15px;background:white; margin-right:400px;"><b>SUB. 
										{{ $subjectListname[$subjectscount['subject_id']]}} &nbsp; FC: 
										 {{ @$subjectscount['fix_code']}} &nbsp;{{ @$subjectscount['cent_code']}} - {{ @$subjectscount['cent_name']}}
						</span>											
					</th>
					</tr>
				</thead> 
				<tbody style="width:100%;">
										
						@php $counter = 0; $showCounter = 1;@endphp 
						@php $type = "studentData";@endphp
				        @if(@$subjectscount[$type] && count($subjectscount[$type]))
						@foreach($subjectscount[$type] as $student)
						@if($counter%2 == 0)
									</tr>
								@endif
										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:18px;"><b>{{@$student->enrollment}}</b></span><br />
											<span style="font-size:16px;"><b>{{@$student->fixcode}}</b></span>
										</td>

										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:18px;"><b>{{@$student->fixcode}}</b></span>
										</td>

						@php $showCounter++;$counter++; @endphp
						@endforeach 
						@endif
								
@endforeach
</tbody>
</table>
</body>
</html>
