
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">बैंक विवरण( Bank Details)</a></span></span></div>
<table border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;Account Holder Name	</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Branch Name &nbsp; </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Account Number</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;IFSC Code</span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Bank Name </span></span></td>
			<td><span class="font"><span class='customStrong'>&nbsp;Linked Mobile</span></span></td>
		</tr>
		<tr>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->account_holder_name	}}</td>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->branch_name}}</td>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->account_number}}</td>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->ifsc_code}}</td>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->bank_name}}</td>
			<td class="font">&nbsp;{{@$masterDetails->bankdetils->linked_mobile}}</td>
		</tr>
	</tbody>
</table>
@include('elements.verification.other_then_document_status_update')
