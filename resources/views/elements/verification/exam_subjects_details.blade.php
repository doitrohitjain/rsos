

@if(!empty($master_subject_details['examSubjectDetails']))
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">परीक्षा विषय विवरण</a>(Examination Subject Details )</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="7"><span class="font"><span class='customStrong'>Examination Subject in which the candidate wants to appear</span></span></td>
		</tr>-->
		<tr>
			@php $exam_subject_count = count($master_subject_details['examSubjectDetails']); @endphp
			@for($i=1;$i <= $exam_subject_count;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		<tr>
		    @foreach(@$master_subject_details['examSubjectDetails'] as $key => $values)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endforeach
		</tr>
	</tbody>
</table>
@endif
@include('elements.verification.other_then_document_status_update')