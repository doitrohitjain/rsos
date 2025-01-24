
 
<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पते का विवरण</style></a>(Address Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
		<tr>
            <td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;पता(Address):</span></span></td>
			<td style="width: 35%;">&nbsp;{{@$masterDetails->address->address1}}</td>
			<td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;राज्य(State):</span></span></td>
			<td style="width: 35%;">&nbsp;{{@$masterDetails->address->state_name}}</td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;ज़िला(District):</span></span></td>
			<td>&nbsp;{{@$masterDetails->address->district_name}}</td>
			<td><span class="font"><span class='customStrong'>&nbsp;ब्लॉक/तहसील(Block/Tehsil):</span></span></td>
			<td>&nbsp;{{@$masterDetails->address->block_name}}/{{@$masterDetails->address->tehsil_name}}</td>
		</tr>
		<tr>
			<td><span class="font"><span class='customStrong'>&nbsp;शहर/गाँव(City/Village):</span></span></td>
			<td>&nbsp;{{@$masterDetails->address->city_name}}</td>
			<td><span class="font"><span class='customStrong'>&nbsp;पिन कोड(Pincode)</span></span></td>
			<td>&nbsp;{{@$masterDetails->address->pincode}}</td>
		</tr>
	</tbody>
</table>

<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">पत्राचार पते का विवरण</style></a>(Correspondence Address Details)</span></span></div>

<table border="1" style="width:100%;">
	<tbody>
       @if(@$masterDetails->address->is_both_same == 1)
		   <tr>
				<td style="width: 35%;"><span class="font"><span class='customStrong'>&nbsp;पत्राचार के समान(Same as Correspondence):</span></span></td>
				<td style="width: 35%;">&nbsp;{{@$yesno[@$masterDetails->address->is_both_same]}}</td>
			</tr>
      @else
			<tr>
				<td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;पता(Address):</span></span></td>
				<td style="width: 35%;">&nbsp;{{@$masterDetails->address->current_address1}}</td>
                <td style="width: 15%;"><span class="font"><span class='customStrong'>&nbsp;राज्य(State):</span></span></td>
				<td style="width: 35%;">&nbsp;{{@$masterDetails->address->current_state_name}}</td>
			</tr>
			<tr>
				<td><span class="font"><span class='customStrong'>&nbsp;ज़िला(District):</span></span></td>
				<td>&nbsp;{{@$masterDetails->address->current_district_name}}</td>
                <td><span class="font"><span class='customStrong'>&nbsp;ब्लॉक/तहसील(Block/Tehsil):</span></span></td>
				<td>&nbsp;{{@$masterDetails->address->current_block_name}}/{{@$masterDetails->address->current_tehsil_name}}</td>
			</tr>
			<tr>
				<td><span class="font"><span class='customStrong'>&nbsp;शहर/गाँव(City/Village):</span></span></td>
				<td>&nbsp;{{@$masterDetails->address->current_city_name}}</td>
                <td><span class="font"><span class='customStrong'>&nbsp;पिन कोड(Pincode)</span></span></td>
				<td>&nbsp;{{@$masterDetails->address->current_pincode}}</td>
			</tr>
			
	    @endif
	</tbody>
</table>
@include('elements.verification.other_then_document_status_update')