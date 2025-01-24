<style>
*{
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}
body{
    font-family: Helvetica;
    -webkit-font-smoothing: antialiased;
);
}
.table-wrapper{
    box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
}

.fl-table {
    border-radius: 5px;
    font-size: 12px;
    font-weight: normal;
    border: none;
    border-collapse: collapse;
    width: 100%;
    max-width: 100%;
    white-space: nowrap;
    background-color: white;
}

.fl-table td, .fl-table th {
    text-align: center;
    padding: 2px;
}

.fl-table td {
    border-right: 1px solid #f8f8f8;
    font-size: 15px;
}

.fl-table thead th {
    color: #ffffff;
    background: #4FC3A1;
}


.fl-table thead th:nth-child(odd) {
    color: #ffffff;
    background: #324960;
}

.fl-table tr:nth-child(even) {
    background: #F8F8F8;
}

 header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                height: 50px;
                font-size: 20px !important;

                /** Extra personal styles **/
                background-color: #008B8B;
                color: white;
                text-align: center;
                line-height: 35px;
            }





</style> 
<!-- Page Length Options -->

<table style="width:100%;">
	<tbody>
		<tr>
			<td style="width:30%"><img alt="" src="https://hte.rajasthan.gov.in/files/uploads/administrator.png" style="width: 60px; height: 49px; float: right;border-radius: 10px" /></span></td>
			<td style="text-align: left;width:40%;"><span style="font-size:15px;"><strong>Rajasthan State Open School 2022-23</strong></span></td>
			<td style="width:30%">&nbsp;</td>
		</tr>
	</tbody>
</table>

<table style="width:100%;">
	<tbody>
		<tr>
			<td style="text-align: center; width: 35%;">&nbsp;</td>
			<td style="vertical-align: top;"><span style="font-size:15px;"><strong>Student Applications Report</strong></span></td>
		</tr>
	</tbody>
</table>
<br>
				<table class="fl-table">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
					<th>Enrollment Number</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Course Type</th>
                    <th>Stream Type</th>
					<th>Admission Type</th>
					<th>Medium</th>
					<th>Lock & Submit</th>
                  </tr>
                </thead>
                <tbody>
				  @php $i=1;@endphp
                  @foreach ($result as $results)
                  <tr>
                    <td>{{@$i}}</td>
					<td>{{@$results->enrollment}}</td>
					<td>{{@$results->name}}</td>
                    <td>{{@$gender_id[$results->gender_id]}}</td>
                    <td>{{@$course[$results->course]}}</td>
                    <td>{{@$stream_id[$results->stream]}}</td>
                    <td>{{@$adm_types[$results->adm_type]}}</td>
					<td>{{@$midium[$results->medium]}}</td>
					<td>{{ (@$results->locksumbitted == 1) ? 'yes' : 'No'}}</td>
                    </tr>
					@php $i++;@endphp
                   @endforeach  
                </tbody>
              </table>
			
		
 





