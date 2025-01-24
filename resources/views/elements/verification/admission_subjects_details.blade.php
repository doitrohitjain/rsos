



@php 
$adm_com_sub_cnt = 0;
$adm_add_sub_cnt = 0;
@endphp
@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
	@php if(@$values['is_additional']==0){ $adm_com_sub_cnt++; }  if(@$values['is_additional']==1){ $adm_add_sub_cnt++; }  @endphp
@endforeach

@if($adm_com_sub_cnt > 0)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">विषय विवरण(Subject Details)</a></span></span></div>
<div><span class="fonthead"><span class='customStrong'>&nbsp;अनिवार्य विषय विवरण(Compulsory Subject Details)</span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class="font"><span class='customStrong'>Compulsory Subject</span></span></td>
		</tr>-->
		<tr>
			@for($i=1;$i <= $adm_com_sub_cnt;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		
		<tr>
			@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
			@if(@$values['is_additional']==0)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endif
			@endforeach
		</tr>
			
	</tbody>
</table>
@endif

@if($adm_add_sub_cnt > 0)
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">अतिरिक्त विषय विवरण(Additional Subject Details)</a></span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<!--<tr>
			<td colspan="5"><span class='customStrong'><span class="font">Additional Subject</span></span></td>
		</tr>-->
		
		<tr>
			@for($i=1;$i <= $adm_add_sub_cnt;$i++) 
			<td><span class='customStrong'><span class="font">&nbsp;Subject{{ $i }}</span></span></td>
			@endfor
		</tr>
		
		<tr>
			@foreach(@$master_subject_details['admissionSubjectDetails'] as $key => $values)
			@if(@$values['is_additional']==1)
			<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
			@endif
			@endforeach
		</tr>
	</tbody>
</table>
@endif

@include('elements.verification.other_then_document_status_update')