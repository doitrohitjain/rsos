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

</style>
</head>
<body>


<table width="100%">
			<thead style="width:100%;">
					<tr class='font'>
						<th colspan="12" style="font-size:15px;background:white;" align="left" >
								<b>SUB. 
										{{ $subjectListname[$subjectscount['subject_id']]}} &nbsp; FC: 
										 {{ @$subjectscount['fix_code']}} &nbsp;{{ @$subjectscount['cent_code']}} - {{ @$subjectscount['cent_name']}}
															
					</th>
					</tr>
				</thead> 
				<tbody style="width:100%;">
										
						@php $counter = 0; @endphp 
						@php $type = "studentData";@endphp
				        @if(@$subjectscount[$type] && count($subjectscount[$type]))
				        @if(count($subjectscount['studentData']) == 1)
                         @foreach($subjectscount[$type] as $student)
						@if($counter%1 == 0)
									</tr>
								@endif
										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:20px;"><b>{{@$student->enrollment}}</b></span><br />
											<span style="font-size:16px;"><b>{{@$student->fixcode}}</b></span>
										</td>

										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:22px;"><b>{{@$student->fixcode}}</b></span>
										</td>
										<td></td>
										<td></td>
										
						@php $counter++; @endphp
						@endforeach
				        @else
						@foreach($subjectscount[$type] as $student)
						@if($counter%2 == 0)
									</tr>
								@endif
										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:20px;"><b>{{@$student->enrollment}}</b></span><br />
											<span style="font-size:16px;"><b>{{@$student->fixcode}}</b></span>
										</td>

										<td class='font' align="center" style="height:92px;padding:0px 0px 0px 0px;width:25%;vertical-align:middle;">
											<span style="font-size:22px;"><b>{{@$student->fixcode}}</b></span>
										</td>

										
						@php $counter++; @endphp
						@endforeach
						@endif 
						@endif
								
@endforeach
</tbody>
</table>
</body>
</html>

