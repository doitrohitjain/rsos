



<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">परीक्षा शुल्क विवरण</a>(Exam Fees Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;Registration Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Additional Subject Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;TOC Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Practical Fees </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Exam Fees</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Late Fees &nbsp; </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Total Fees</span></span></td>
		</tr>
		<tr>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->registration_fees}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->add_sub_fees}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->toc_fees}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->practical_fees}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->readm_exam_fees}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->late_fee}}</td>
			<td class="font">&nbsp;{{@$masterDetails->studentfees->total}}</td>
		</tr>
	</tbody>
</table>
@include('elements.verification.other_then_document_status_update')